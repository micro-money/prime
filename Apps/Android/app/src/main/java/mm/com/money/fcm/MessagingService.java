package mm.com.money.fcm;

import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.media.RingtoneManager;
import android.net.Uri;
import android.support.v4.app.NotificationCompat;
import android.support.v4.app.TaskStackBuilder;
import android.text.TextUtils;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

import mm.com.money.R;
import mm.com.money.Settings;
import mm.com.money.ui.MainActivity;

/**
 * Created by Ruslan Mingaliev on 14/03/2017
 * Email: mingaliev.rr@gmail.com
 * Skype: doinktheclown_ln
 * All rights reserved.
 */

public class MessagingService extends FirebaseMessagingService {
    public static final String ACTION_NOTIFICATION = MessagingService.class.getCanonicalName() + ".ACTION_NOTIFICATION";
    public static final String EXTRA_DATA_TEXT = MessagingService.class.getCanonicalName() + ".NOTIFICATION_TEXT";
    public static final String EXTRA_DATA_TITLE = MessagingService.class.getCanonicalName() + ".NOTIFICATION_TITLE";

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        String text = remoteMessage.getData().get("text");
        String title = remoteMessage.getData().get("title");
        sendPushNotification(title, text);
    }

    /**
     * Показ push-уведомления
     * @param message - сообщение для показа
     */
    private void sendPushNotification(String title, String message) {
        Intent resultIntent = new Intent(this, MainActivity.class);
        resultIntent.setFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP);
        resultIntent.setAction(ACTION_NOTIFICATION);
        resultIntent.putExtra(EXTRA_DATA_TEXT, message);
        resultIntent.putExtra(EXTRA_DATA_TITLE, title);

        PendingIntent pendingIntent =
                PendingIntent.getActivity(this, 0, resultIntent, PendingIntent.FLAG_UPDATE_CURRENT);

        Uri defaultSoundUri= RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);
        NotificationCompat.Builder notificationBuilder = new NotificationCompat.Builder(this)
                .setSmallIcon(R.mipmap.ic_launcher)
                .setContentTitle(TextUtils.isEmpty(title) ? getString(R.string.app_name) : title)
                .setContentText(message)
                .setAutoCancel(true)
                .setDefaults(Notification.DEFAULT_ALL)
                .setSound(defaultSoundUri)
                .setContentIntent(pendingIntent);

        NotificationManager notificationManager =
                (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

        notificationManager.notify(0 /* ID of notification */, notificationBuilder.build());
    }
}
