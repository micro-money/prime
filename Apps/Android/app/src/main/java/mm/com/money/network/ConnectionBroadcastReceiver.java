package mm.com.money.network;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;

/**
 * Created by Ruslan Mingaliev on 17/03/2017
 * Email: mingaliev.rr@gmail.com
 * Skype: doinktheclown_ln
 * All rights reserved.
 */

public class ConnectionBroadcastReceiver extends BroadcastReceiver {
    private OnConnectionListener mListener;
    public ConnectionBroadcastReceiver(OnConnectionListener listener) {
        mListener = listener;
    }

    @Override
    public void onReceive(Context context, Intent intent) {

        ConnectivityManager cm = (ConnectivityManager)
                context.getSystemService(Context.CONNECTIVITY_SERVICE);

        NetworkInfo netInfo = cm.getActiveNetworkInfo();
        if (netInfo != null && netInfo.isConnectedOrConnecting()) {
            mListener.reload();
        }
    }
}
