package mm.com.money;

import android.app.Application;

/**
 * Created by Ruslan Mingaliev on 15/03/2017
 * Email: mingaliev.rr@gmail.com
 * Skype: doinktheclown_ln
 * All rights reserved.
 */

public class MicromoneyApp extends Application {
    @Override
    public void onCreate() {
        super.onCreate();
        Settings.init(this);
    }
}
