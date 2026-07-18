<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use ZipArchive;

class WordPreviewService
{
    private const WORD_NAMESPACE = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    public function toHtml(string $path): ?string
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return null;
        }

        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (! $documentXml) {
            return null;
        }

        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadXML($documentXml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return null;
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('w', self::WORD_NAMESPACE);
        $body = $xpath->query('//w:body')->item(0);

        if (! $body) {
            return null;
        }

        $html = '';
        foreach ($body->childNodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $html .= match ($node->localName) {
                'p' => $this->paragraph($node, $xpath),
                'tbl' => $this->table($node, $xpath),
                default => '',
            };
        }

        return trim($html) !== '' ? $html : '<p>الملف لا يحتوي على نص قابل للعرض.</p>';
    }

    private function paragraph(DOMElement $paragraph, DOMXPath $xpath): string
    {
        $content = '';
        foreach ($xpath->query('.//w:r', $paragraph) as $run) {
            $content .= $this->run($run, $xpath);
        }

        $alignment = $xpath->query('./w:pPr/w:jc', $paragraph)->item(0);
        $alignmentValue = $alignment instanceof DOMElement
            ? $alignment->getAttributeNS(self::WORD_NAMESPACE, 'val')
            : '';
        $textAlign = match ($alignmentValue) {
            'center' => 'center',
            'left' => 'left',
            'right' => 'right',
            'both', 'distribute' => 'justify',
            default => 'start',
        };

        return '<p dir="auto" style="text-align:' . $textAlign . '">' . ($content !== '' ? $content : '<br>') . '</p>';
    }

    private function run(DOMNode $run, DOMXPath $xpath): string
    {
        $content = '';
        foreach ($run->childNodes as $child) {
            if (! $child instanceof DOMElement || $child->localName === 'rPr') {
                continue;
            }

            $content .= match ($child->localName) {
                't', 'instrText' => htmlspecialchars($child->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                'tab' => '&emsp;',
                'br', 'cr' => '<br>',
                default => '',
            };
        }

        if ($content === '') {
            return '';
        }

        if ($xpath->query('./w:rPr/w:u', $run)->length > 0) {
            $content = '<u>' . $content . '</u>';
        }
        if ($xpath->query('./w:rPr/w:i', $run)->length > 0) {
            $content = '<em>' . $content . '</em>';
        }
        if ($xpath->query('./w:rPr/w:b', $run)->length > 0) {
            $content = '<strong>' . $content . '</strong>';
        }

        return $content;
    }

    private function table(DOMElement $table, DOMXPath $xpath): string
    {
        $html = '<div class="word-table-wrap"><table class="word-table"><tbody>';

        foreach ($xpath->query('./w:tr', $table) as $row) {
            $html .= '<tr>';
            foreach ($xpath->query('./w:tc', $row) as $cell) {
                $cellHtml = '';
                foreach ($xpath->query('./w:p', $cell) as $paragraph) {
                    $cellHtml .= $this->paragraph($paragraph, $xpath);
                }
                $html .= '<td>' . ($cellHtml !== '' ? $cellHtml : '&nbsp;') . '</td>';
            }
            $html .= '</tr>';
        }

        return $html . '</tbody></table></div>';
    }
}
