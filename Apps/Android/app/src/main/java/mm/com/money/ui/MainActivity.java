package mm.com.money.ui;

import android.Manifest;
import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.app.Activity;
import android.content.ActivityNotFoundException;
import android.content.BroadcastReceiver;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.pm.PackageManager;
import android.net.ConnectivityManager;
import android.net.Uri;
import android.support.annotation.NonNull;
import android.support.v4.app.ActivityCompat;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.KeyEvent;
import android.view.View;
import android.webkit.CookieManager;
import android.webkit.ValueCallback;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ProgressBar;

import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.List;

import mm.com.money.Ln;
import mm.com.money.R;
import mm.com.money.Settings;
import mm.com.money.fcm.MessagingService;
import mm.com.money.location.LocationManager;
import mm.com.money.network.ApiManager;
import mm.com.money.network.ConnectionBroadcastReceiver;
import mm.com.money.network.OnConnectionListener;

public class MainActivity extends AppCompatActivity implements OnConnectionListener {

    private static final int PERMISSIONS_REQUEST_CODE = 1003;

    private WebView mWebView;
    private WebView mProgress;
    private String mCookie = null;
    private LocationManager mLocationManager;

    public final static int FILECHOOSER_RESULTCODE = 1;
    public final static int FILECHOOSER_RESULTCODE_FOR_ANDROID_5 = 2;
    private ValueCallback<Uri[]> mUploadMessageForAndroid5;
    private ValueCallback<Uri> mUploadMessage;
    private boolean mIsOnline = true;
    private String mLastUrl = "";
    private BroadcastReceiver mConnectionReceiver;

    @SuppressLint({"AddJavascriptInterface", "SetJavaScriptEnabled"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        if (MessagingService.ACTION_NOTIFICATION.equals(getIntent().getAction())) {
            showNotificationMessage(getIntent());
        }

        mLocationManager = new LocationManager(this);

        mWebView = (WebView) findViewById(R.id.webview);
        mProgress = (WebView) findViewById(R.id.progress);
        mProgress.loadUrl("file:///android_asset/loading.html");

        mWebView.setWebChromeClient(new WebChromeClient() {
            @Override
            public void onProgressChanged(WebView view, int progress) {
                if(progress < 100 && mProgress.getVisibility() == ProgressBar.GONE){
                    mProgress.reload();
                    mProgress.setVisibility(ProgressBar.VISIBLE);
                    mWebView.setVisibility(View.GONE);
                }

                if(progress == 100) {
                    mProgress.setVisibility(ProgressBar.GONE);
                    mWebView.setVisibility(View.VISIBLE);
                }
            }

            public void openFileChooser(ValueCallback<Uri> uploadMsg, String acceptType) {
                openFileChooserImpl(uploadMsg);
            }

            //3.0--
            public void openFileChooser(ValueCallback<Uri> uploadMsg) {
                openFileChooserImpl(uploadMsg);
            }

            public void openFileChooser(ValueCallback<Uri> uploadMsg, String acceptType, String capture) {
                openFileChooserImpl(uploadMsg);
            }

            // For Android > 5.0
            public boolean onShowFileChooser(WebView webView, ValueCallback<Uri[]> uploadMsg, WebChromeClient.FileChooserParams fileChooserParams) {
                openFileChooserImplForAndroid5(uploadMsg);
                return true;
            }
        });

        mWebView.setWebViewClient(new WebViewClient() {
            @TargetApi(24)
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                Ln.d("Loading: " + request.getUrl().toString());
                // Если схема не http, то обрабатываем её
                if (request.getUrl().toString().startsWith("http:") || request.getUrl().toString().startsWith("https:")) {
                    return false;
                }
                parseScheme(request.getUrl());
                return true;
            }

            @SuppressWarnings("deprecation")
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                Ln.d("Loading: " + url);
                // Если схема не http, то обрабатываем её
                if (url.startsWith("http:") || url.startsWith("https:")) {
                    return false;
                }
                parseScheme(Uri.parse(url));
                return true;
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                Ln.d("loading finished: " + url);
                // Если пользователь отправил данные, то проверяем наличие cookie и пытаемся получить Mapl
                if (url.contains("app_wizard")) {
                    String cookies = CookieManager.getInstance().getCookie(url);
                    if (mCookie == null) {
                            mCookie = cookies;
                            ApiManager.getInstance().getMapl(MainActivity.this, mCookie);
                    }
                }
            }

            @SuppressWarnings("deprecation")
            @Override
            public void onReceivedError(WebView view, int errorCode, String description, String failingUrl) {
                mWebView.loadUrl("file:///android_asset/error.html");
                mLastUrl = failingUrl;
                mIsOnline = false;
            }

            @TargetApi(21)
            @Override
            public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
                mWebView.loadUrl("file:///android_asset/error.html");
                mLastUrl = request.getUrl().toString();
                mIsOnline = false;
            }
        });
        mWebView.getSettings().setAllowFileAccess(true);
        mWebView.getSettings().setJavaScriptEnabled(true);
        mWebView.getSettings().setDomStorageEnabled(true);

        WebSettings settings = mWebView.getSettings();
        // settings.setPluginsEnabled(true);
        methodInvoke(settings, "setPluginsEnabled", new Class[] { boolean.class }, new Object[] { true });
        // settings.setPluginState(PluginState.ON);
        methodInvoke(settings, "setPluginState", new Class[] { WebSettings.PluginState.class }, new Object[] { WebSettings.PluginState.ON });
        // settings.setPluginsEnabled(true);
        methodInvoke(settings, "setPluginsEnabled", new Class[] { boolean.class }, new Object[] { true });
        // settings.setAllowUniversalAccessFromFileURLs(true);
        methodInvoke(settings, "setAllowUniversalAccessFromFileURLs", new Class[] { boolean.class }, new Object[] { true });
        // settings.setAllowFileAccessFromFileURLs(true);
        methodInvoke(settings, "setAllowFileAccessFromFileURLs", new Class[] { boolean.class }, new Object[] { true });

        mProgress.getSettings().setJavaScriptEnabled(true);
        mWebView.loadUrl(Settings.BASE_URL);

        requestPermissions();
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if ((keyCode == KeyEvent.KEYCODE_BACK) && mWebView.canGoBack()) {
            mWebView.goBack();
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    /**
     * Парсинг схемы URI
     * @param uri - URI
     */
    private void parseScheme(Uri uri) {
        if ("file:///android_asset/error.html".equals(uri.toString())) {
            mWebView.goBack();
            return;
        }
        Intent intent = new Intent(Intent.ACTION_VIEW);
        intent.setData(uri);
        try {
            startActivity(intent);
        } catch (ActivityNotFoundException e) {
            showMessage(getString(R.string.error_app_not_found));
        }
    }

    private void showMessage(String message) {
        new AlertDialog.Builder(this)
                .setCancelable(false)
                .setPositiveButton(android.R.string.ok, null)
                .setMessage(message)
                .create()
                .show();
    }

    private void showMessage(String title, String message) {
        new AlertDialog.Builder(this)
                .setCancelable(false)
                .setPositiveButton(android.R.string.ok, null)
                .setMessage(message)
                .setTitle(title)
                .create()
                .show();
    }

    private void showMessage(int stringRes) {
        showMessage(getString(stringRes));
    }


    @Override
    protected void onResume() {
        super.onResume();
        mConnectionReceiver = new ConnectionBroadcastReceiver(this);
        registerReceiver(mConnectionReceiver, new IntentFilter(ConnectivityManager.CONNECTIVITY_ACTION));
    }

    @Override
    public void onPause() {
        super.onPause();
        if (mConnectionReceiver != null) {
            unregisterReceiver(mConnectionReceiver);
            mConnectionReceiver = null;
        }
    }

    public LocationManager getLocationManager() {
        return mLocationManager;
    }

    /**
     * Запрос разрешений
     */
    private void requestPermissions() {
        boolean readPhoneState = ContextCompat.checkSelfPermission(this, Manifest.permission.READ_PHONE_STATE) == PackageManager.PERMISSION_GRANTED;
        boolean accessLocation = ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED;
        boolean readContacts = ContextCompat.checkSelfPermission(this, Manifest.permission.READ_CONTACTS) == PackageManager.PERMISSION_GRANTED;

        if (!readPhoneState || !accessLocation || !readContacts) {
            List<String> permissions = new ArrayList<>();
            if (!readPhoneState)
                permissions.add(Manifest.permission.READ_PHONE_STATE);
            if (!accessLocation)
                permissions.add(Manifest.permission.ACCESS_FINE_LOCATION);
            if (!readContacts)
                permissions.add(Manifest.permission.READ_CONTACTS);
            ActivityCompat.requestPermissions(this, permissions.toArray(new String[permissions.size()]), PERMISSIONS_REQUEST_CODE);
        }
    }

    /**
     * Проверка полученных разрешений. Если получено разрешение на геопозицию, то вызывется метод обновления
     * местоположения в {@link mm.com.money.location.LocationService}
     * @param requestCode - код запроса
     * @param permissions - разрешения
     * @param grantResults - результаты
     */
    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {
            mLocationManager.reconnect();
        }
    }

    private void openFileChooserImpl(ValueCallback<Uri> uploadMsg) {
        mUploadMessage = uploadMsg;
                            Intent i = new Intent(Intent.ACTION_GET_CONTENT);
                            i.addCategory(Intent.CATEGORY_OPENABLE);
                            i.setType("image/*");
                            startActivityForResult(Intent.createChooser(i, "File Chooser"), FILECHOOSER_RESULTCODE);
    }

    private void openFileChooserImplForAndroid5(ValueCallback<Uri[]> uploadMsg) {
        mUploadMessageForAndroid5 = uploadMsg;
                            Intent contentSelectionIntent = new Intent(Intent.ACTION_GET_CONTENT);
                            contentSelectionIntent.addCategory(Intent.CATEGORY_OPENABLE);
                            contentSelectionIntent.setType("image/*");

                            Intent chooserIntent = new Intent(Intent.ACTION_CHOOSER);
                            chooserIntent.putExtra(Intent.EXTRA_INTENT, contentSelectionIntent);
                            chooserIntent.putExtra(Intent.EXTRA_TITLE, "Image Chooser");

                            startActivityForResult(chooserIntent, FILECHOOSER_RESULTCODE_FOR_ANDROID_5);
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent intent) {
        if (requestCode == FILECHOOSER_RESULTCODE) {
            if (null == mUploadMessage)
                return;
            Uri result = intent == null || resultCode != Activity.RESULT_OK ? null : intent.getData();
            mUploadMessage.onReceiveValue(result);
            mUploadMessage = null;

        } else if (requestCode == FILECHOOSER_RESULTCODE_FOR_ANDROID_5) {
            if (null == mUploadMessageForAndroid5)
                return;
            Uri result;

            if (intent == null || resultCode != Activity.RESULT_OK) {
                result = null;
            } else {
                result = intent.getData();
            }

            if (result != null) {
                mUploadMessageForAndroid5.onReceiveValue(new Uri[]{result});
            } else {
                mUploadMessageForAndroid5.onReceiveValue(new Uri[]{});
            }
            mUploadMessageForAndroid5 = null;
        }
    }

    @Override
    public void reload() {
        mIsOnline = true;
        if (!TextUtils.isEmpty(mLastUrl))
            mWebView.goBack();
    }

    @Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        if (MessagingService.ACTION_NOTIFICATION.equals(intent.getAction())) {
            showNotificationMessage(intent);
        }
    }

    private void showNotificationMessage(Intent intent) {
        String text = intent.getStringExtra(MessagingService.EXTRA_DATA_TEXT);
        String title = intent.getStringExtra(MessagingService.EXTRA_DATA_TITLE);
        if (TextUtils.isEmpty(title)) title = getString(R.string.app_name);
        if (!TextUtils.isEmpty(text)) {
            showMessage(title, text);
        }
    }

    private final static Object methodInvoke(Object obj, String method, Class<?>[] parameterTypes, Object[] args) {
        try {
            Method m = obj.getClass().getMethod(method, new Class[] { boolean.class });
            m.invoke(obj, args);
        } catch (Exception e) {
            e.printStackTrace();
        }

        return null;
    }
}
