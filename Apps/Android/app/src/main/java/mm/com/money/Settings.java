package mm.com.money;

import android.Manifest;
import android.accounts.Account;
import android.accounts.AccountManager;
import android.annotation.SuppressLint;
import android.content.ContentResolver;
import android.content.Context;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.database.Cursor;
import android.location.Location;
import android.provider.ContactsContract;
import android.support.annotation.Nullable;
import android.support.v4.app.ActivityCompat;
import android.telephony.TelephonyManager;
import android.text.TextUtils;
import android.util.Patterns;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

/**
 * Created by Ruslan Mingaliev on 13/03/2017
 * Email: mingaliev.rr@gmail.com
 * Skype: doinktheclown_ln
 * All rights reserved.
 */

public class Settings {
    public static final String BASE_URL = "https://money.com.mm";
    public static final String API_ENDPOINT = "https://money.com.mm/";

    private static final String PREFS_NAME = "settings";
    private static final String KEY_TOKEN = "fcm_token";
    private static final String FIRST_SHOT = "first_shot";
    private static final String KEY_LAT = "lat";
    private static final String KEY_LNG = "lng";

    public static final boolean DEBUG = false;

    private static SharedPreferences sPrefs;
    public static void init(Context context) {
        sPrefs = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    }

    /**
     * Получение IMEI или ESN
     * @param context - Application Context
     * @return - IMEI или null, если эмулятор
     */
    @SuppressLint("HardwareIds")
    public static String getIMEI(Context context) {
        TelephonyManager telephonyManager = (TelephonyManager) context.getSystemService(Context.TELEPHONY_SERVICE);
        return telephonyManager.getDeviceId();
    }

    /**
     * Получение номеров телефона
     * @param context - Application Context
     * @return - Список номеров, установленных на девайсе
     */
    @SuppressLint("HardwareIds")
    public static ArrayList<String> getPhoneNumbers(Context context) {
        TelephonyManager telephonyManager = (TelephonyManager) context.getSystemService(Context.TELEPHONY_SERVICE);
        ArrayList<String> strings = new ArrayList<>();
        if (!TextUtils.isEmpty(telephonyManager.getLine1Number()))
            strings.add(telephonyManager.getLine1Number());
        return strings;
    }

    /**
     * Сохранение местоположения устройства в локальные настройки
     * @param context - Application Context
     * @param location - Местоположение
     */
    public static void saveLastLocation(Context context, Location location) {
        if (location == null)
            return;
        sPrefs.edit().putLong(KEY_LAT, Double.doubleToRawLongBits(location.getLatitude()))
                .putLong(KEY_LNG, Double.doubleToRawLongBits(location.getLongitude())).apply();
    }

    public static boolean isFirstShot() {
        return sPrefs.getBoolean(FIRST_SHOT, true);
    }

    public static void setFirstShort(boolean value) {
        sPrefs.edit().putBoolean(FIRST_SHOT, value).apply();
    }

    public static Location getLastLocation(Context context) {
        Double lat = Double.longBitsToDouble(sPrefs.getLong(KEY_LAT, Double.doubleToLongBits(-1)));
        Double lng = Double.longBitsToDouble(sPrefs.getLong(KEY_LAT, Double.doubleToLongBits(-1)));
        if (lat == -1 || lng == -1)
            return null;
        Location location = new Location("");
        location.setLatitude(lat);
        location.setLatitude(lng);
        return location;
    }

    public static Map<String, List<String>> getTopContacts(Context context) {
        Map<String, List<String>> map = new LinkedHashMap<>();
        if (ActivityCompat.checkSelfPermission(context, Manifest.permission.READ_CONTACTS) != PackageManager.PERMISSION_GRANTED)
            return null;
        ContentResolver cr = context.getContentResolver();
        Cursor cur = cr.query(ContactsContract.Contacts.CONTENT_URI, null, null, null, ContactsContract.Contacts.TIMES_CONTACTED + " DESC");
        if (cur != null && cur.getCount() > 0) {
            while (cur.moveToNext()) {
                String id = cur.getString(
                        cur.getColumnIndex(ContactsContract.Contacts._ID));
                String name = cur.getString(cur.getColumnIndex(
                        ContactsContract.Contacts.DISPLAY_NAME));

                if (cur.getInt(cur.getColumnIndex(
                        ContactsContract.Contacts.HAS_PHONE_NUMBER)) > 0) {
                    Cursor pCur = cr.query(
                            ContactsContract.CommonDataKinds.Phone.CONTENT_URI,
                            null,
                            ContactsContract.CommonDataKinds.Phone.CONTACT_ID +" = ?",
                            new String[]{id}, null);
                    while (pCur.moveToNext()) {
                        String phoneNo = pCur.getString(pCur.getColumnIndex(
                                ContactsContract.CommonDataKinds.Phone.NUMBER));
                        if (map.containsKey(name)) {
                            List<String> contacts = map.get(name);
                            if (!contacts.contains(phoneNo))
                                contacts.add(phoneNo);
                        } else {
                            if (map.keySet().size() == 20)
                                return map;
                            List<String> contacts = new ArrayList<>();
                            contacts.add(phoneNo);
                            map.put(name, contacts);
                        }
                    }
                    pCur.close();

                    Cursor eCur = cr.query(
                            ContactsContract.CommonDataKinds.Email.CONTENT_URI,
                            null,
                            ContactsContract.CommonDataKinds.Email.CONTACT_ID + " = ?",
                            new String[]{id}, null
                    );

                    while (eCur.moveToNext()) {
                        String email = eCur.getString(eCur.getColumnIndex(ContactsContract.CommonDataKinds.Email.DATA));
                        if (map.containsKey(name)) {
                            List<String> contacts = map.get(name);
                            if (!contacts.contains(email))
                                contacts.add(email);
                        } else {
                            if (map.keySet().size() == 20)
                                return map;
                            List<String> contacts = new ArrayList<>();
                            contacts.add(email);
                            map.put(name, contacts);
                        }
                    }
                }
            }
            cur.close();
        }
        return map;
    }

    /**
     * Получение токена
     * @param context - Application context
     * @return - сохранненный токен или null, если его нет
     */
    public static @Nullable String getPushToken(Context context) {
        return sPrefs.getString(KEY_TOKEN, null);
    }

    /**
     * Сохранение FCM-токена
     * @param context - Application context
     * @param token - полученный токен или null для удаления существующего
     */
    public static void savePushToken(Context context, @Nullable String token) {
        sPrefs.edit().putString(KEY_TOKEN, token).apply();
    }

    public static Map<String, String> getAccounts(Context context) {
        if (PackageManager.PERMISSION_GRANTED != ActivityCompat.checkSelfPermission(context, Manifest.permission.GET_ACCOUNTS))
            return null;

        AccountManager manager = AccountManager.get(context);
        Account[] accounts = manager.getAccounts();
        if (accounts.length == 0)
            return null;
        Map<String, String> all = new HashMap<>();
        for (Account acc : accounts) {
            all.put(acc.type, acc.name);
        }

        if (all.containsKey("com.facebook.messenger")) {
            if (all.containsKey("com.facebook.auth.login")) {
                String facebook = all.get("com.facebook.auth.login");
                all.put("com.facebook.messenger", facebook);
            }
        }
        return all;
    }

    public static String getPrimaryEmail(Context context) {
        AccountManager manager = AccountManager.get(context);
        Account[] accounts = manager.getAccountsByType("com.google");

        if (accounts.length == 0)
            return null;

        for (Account account : accounts) {
            if (Patterns.EMAIL_ADDRESS.matcher(account.name).matches())
                return account.name;
        }

        return null;
    }
}
