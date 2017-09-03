package mm.com.money;

import android.util.Log;

/**
 * Created by Ruslan Mingaliev on 11/11/2016
 * Email: mingaliev.rr@gmail.com
 * Skype: doinktheclown_ln
 * All rights reserved.
 */

public class Ln {
    private static final String TAG = "Micromoney";

    public static void d(String message) {
        if (Settings.DEBUG)
            Log.d(TAG, message);
    }

    public static void e(Throwable e) {
        if (Settings.DEBUG) {
            Log.e(TAG, e.getMessage(), e);
        }
    }
}
