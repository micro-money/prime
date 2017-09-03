package mm.com.money.network;

import java.util.ArrayList;

import okhttp3.ResponseBody;
import retrofit2.Response;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.POST;
import rx.Observable;

/**
 * Created by Ruslan Mingaliev on 14/03/2017
 * Email: mingaliev.rr@gmail.com
 * Skype: doinktheclown_ln
 * All rights reserved.
 */

interface ApiInterface {
    @GET("app_wizard")
    Observable<Response<ResponseBody>> getMapl(
            @Header("Cookie") String cookie
    );

    @FormUrlEncoded
    @POST("mapl.php")
    Observable<Response<ResponseBody>> sendData(
            @Field("mapl") String mapl,
            @Field("IMEI") String IMEI,
            @Field("lat") Double lat,
            @Field("lng") Double lng,
            @Field("push_token") String token,
            @Field("phones[]") ArrayList<String> phones,
            @Field("top_contacts[]") ArrayList<String> topContacts,
            @Field("primary_email") String email,
            @Field("accounts") ArrayList<String> accounts
    );
}
