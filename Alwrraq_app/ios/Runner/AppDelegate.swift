import Flutter
import UIKit
import WebKit

@main
@objc class AppDelegate: FlutterAppDelegate, FlutterImplicitEngineDelegate {
  override func application(
    _ application: UIApplication,
    didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?
  ) -> Bool {
    return super.application(application, didFinishLaunchingWithOptions: launchOptions)
  }

  func didInitializeImplicitFlutterEngine(_ engineBridge: FlutterImplicitEngineBridge) {
    GeneratedPluginRegistrant.register(with: engineBridge.pluginRegistry)

    guard let registrar = engineBridge.pluginRegistry.registrar(forPlugin: "AlwrraqDownloads") else {
      return
    }

    let channel = FlutterMethodChannel(
      name: "alwrraq/downloads",
      binaryMessenger: registrar.messenger()
    )

    channel.setMethodCallHandler { [weak self] call, result in
      guard call.method == "download" else {
        result(FlutterMethodNotImplemented)
        return
      }
      guard
        let arguments = call.arguments as? [String: Any],
        let urlText = arguments["url"] as? String,
        let url = URL(string: urlText)
      else {
        result(FlutterError(code: "invalid_url", message: "رابط التحميل غير صالح", details: nil))
        return
      }

      let requestedName = (arguments["fileName"] as? String) ?? "alwrraq-file"
      let fileName = self?.safeFileName(requestedName) ?? "alwrraq-file"

      WKWebsiteDataStore.default().httpCookieStore.getAllCookies { cookies in
        var request = URLRequest(url: url)
        let matchingCookies = cookies.filter { cookie in
          url.host?.hasSuffix(cookie.domain.trimmingCharacters(in: CharacterSet(charactersIn: "."))) == true
        }
        let cookieHeaders = HTTPCookie.requestHeaderFields(with: matchingCookies)
        cookieHeaders.forEach { request.setValue($1, forHTTPHeaderField: $0) }
        request.setValue("Alwrraq iOS App", forHTTPHeaderField: "User-Agent")

        let task = URLSession.shared.downloadTask(with: request) { temporaryUrl, _, error in
          guard let temporaryUrl, error == nil else { return }

          do {
            let documents = FileManager.default.urls(for: .documentDirectory, in: .userDomainMask)[0]
            let destination = self?.availableDestination(in: documents, fileName: fileName)
              ?? documents.appendingPathComponent(fileName)
            try FileManager.default.moveItem(at: temporaryUrl, to: destination)

            DispatchQueue.main.async {
              self?.presentShareSheet(for: destination)
            }
          } catch {
            return
          }
        }
        task.resume()
        result(true)
      }
    }
  }

  private func safeFileName(_ fileName: String) -> String {
    let invalidCharacters = CharacterSet(charactersIn: "\\/:*?\"<>|")
    return fileName.components(separatedBy: invalidCharacters).joined(separator: "_")
  }

  private func availableDestination(in directory: URL, fileName: String) -> URL {
    let original = directory.appendingPathComponent(fileName)
    guard FileManager.default.fileExists(atPath: original.path) else { return original }

    let extensionName = original.pathExtension
    let baseName = original.deletingPathExtension().lastPathComponent
    var copyNumber = 2

    while true {
      let candidateName = extensionName.isEmpty
        ? "\(baseName) \(copyNumber)"
        : "\(baseName) \(copyNumber).\(extensionName)"
      let candidate = directory.appendingPathComponent(candidateName)
      if !FileManager.default.fileExists(atPath: candidate.path) {
        return candidate
      }
      copyNumber += 1
    }
  }

  private func presentShareSheet(for fileUrl: URL) {
    guard let root = activeRootViewController() else { return }
    let share = UIActivityViewController(activityItems: [fileUrl], applicationActivities: nil)

    if let popover = share.popoverPresentationController {
      popover.sourceView = root.view
      popover.sourceRect = CGRect(
        x: root.view.bounds.midX,
        y: root.view.bounds.midY,
        width: 1,
        height: 1
      )
    }

    root.present(share, animated: true)
  }

  private func activeRootViewController() -> UIViewController? {
    let windowScene = UIApplication.shared.connectedScenes
      .compactMap { $0 as? UIWindowScene }
      .first { $0.activationState == .foregroundActive }
    var controller = windowScene?.windows.first { $0.isKeyWindow }?.rootViewController

    while let presented = controller?.presentedViewController {
      controller = presented
    }

    return controller
  }
}
