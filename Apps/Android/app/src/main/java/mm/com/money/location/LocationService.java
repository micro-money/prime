package mm.com.money.location;

import android.Manifest;
import android.app.Service;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.location.Location;
import android.os.Binder;
import android.os.Bundle;
import android.os.IBinder;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.ActivityCompat;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.location.LocationListener;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.location.LocationServices;

import mm.com.money.Ln;
import mm.com.money.Settings;
import rx.subjects.PublishSubject;

/**
 * Created by Ruslan Mingaliev on 09/02/2017
 * Email: mingaliev.rr@gmail.com
 * Skype: doinktheclown_ln
 * All rights reserved.
 */

public class LocationService extends Service
        implements GoogleApiClient.ConnectionCallbacks, GoogleApiClient.OnConnectionFailedListener, LocationListener {

    private static final long INTERVAL = 5000L;

    private GoogleApiClient mLocationClient;
    private LocationRequest mLocationRequest;
    private LocationBinder mBinder = new LocationBinder();
    private PublishSubject<Location> mLocationSubject = PublishSubject.create();
    private Location mLastLocation = null;

    @Override
    public void onCreate() {
        super.onCreate();
        mLocationClient
                = new GoogleApiClient.Builder(getApplicationContext()).addApi(LocationServices.API)
                .addConnectionCallbacks(this)
                .addOnConnectionFailedListener(this)
                .build();

        mLocationClient.connect();
    }

    @Override
    public void onLocationChanged(Location location) {
        Settings.saveLastLocation(this, location);
        mLocationSubject.onNext(location);
        mLastLocation = location;
    }

    @Override
    public void onConnected(@Nullable Bundle bundle) {
        mLocationRequest = new LocationRequest();
        mLocationRequest.setInterval(INTERVAL)
                .setPriority(LocationRequest.PRIORITY_BALANCED_POWER_ACCURACY)
                .setFastestInterval(INTERVAL);
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION)
                != PackageManager.PERMISSION_GRANTED
                && ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION)
                != PackageManager.PERMISSION_GRANTED) {
            Ln.d("Permissions not granted");
            return;
        }

        LocationServices.FusedLocationApi.requestLocationUpdates(mLocationClient, mLocationRequest, this);
    }

    @Override
    public void onDestroy() {
        try {
            LocationServices.FusedLocationApi.removeLocationUpdates(mLocationClient, this);
        } catch (IllegalStateException e) { Ln.e(e); }
    }

    @Override
    public void onConnectionSuspended(int i) {

    }

    @Override
    public void onConnectionFailed(@NonNull ConnectionResult connectionResult) {

    }

    @Nullable
    @Override
    public IBinder onBind(Intent intent) {
        return mBinder;
    }

    public class LocationBinder extends Binder {
        public LocationService getService() {
            return LocationService.this;
        }
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        return START_NOT_STICKY;
    }

    public Location getLastLocation() {
        return mLastLocation;
    }

    public PublishSubject<Location> getSubject() {
        return mLocationSubject;
    }

    public void reconnect() {
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION)
                == PackageManager.PERMISSION_GRANTED
                || ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION)
                == PackageManager.PERMISSION_GRANTED) {
            try {
                LocationServices.FusedLocationApi.requestLocationUpdates(mLocationClient, mLocationRequest, this);
            } catch (IllegalStateException e) { Ln.e(e); }
        }
    }
}
