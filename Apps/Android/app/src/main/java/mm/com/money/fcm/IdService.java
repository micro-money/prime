package mm.com.money.fcm;

import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.iid.FirebaseInstanceIdService;

import mm.com.money.Settings;

/**
 * Created by Ruslan Mingaliev on 14/03/2017
 * Email: mingaliev.rr@gmail.com
 * Skype: doinktheclown_ln
 * All rights reserved.
 */

public class IdService extends FirebaseInstanceIdService {

    /**
     * Сохранение токена при его обновлении
     */
    @Override
    public void onTokenRefresh() {
        String refreshedToken = FirebaseInstanceId.getInstance().getToken();
        Settings.savePushToken(this, refreshedToken);
    }
}
