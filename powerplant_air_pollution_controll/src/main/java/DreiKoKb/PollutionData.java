package DreiKoKb;

import javax.json.*;
import javax.enterprise.context.ApplicationScoped;
import javax.ws.rs.QueryParam;
import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.client.Client;
import javax.ws.rs.client.ClientBuilder;
import javax.ws.rs.client.WebTarget;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import java.io.InputStream;

@Path("/pol")
public class PollutionData {


    @GET
    @Produces(MediaType.TEXT_PLAIN)
    public String hello() {
        return "Hello RESTEasy";
    }



    private static final String POL_URL = /*
     * "http://api.openweathermap.org/data/2.5/air_pollution"
     */ "http://api.openweathermap.org/data/2.5/air_pollution";

    private static final String HIS_URL = /*
     * "http://api.openweathermap.org/data/2.5/air_pollution/history"
     */ "http://api.openweathermap.org/data/2.5/air_pollution/history";

    private static final String CIT_URL = /*
     * "localhost:8080/cityconvert/"
     */ "localhost:8080/cityconvert/";

    private static final String GEO_URL = /*
     * "http://api.openweathermap.org/geo/1.0/direct"
     */ "http://api.openweathermap.org/geo/1.0/direct";

    // APIKEY: 2f102b6aceecd97fb83b1fc6dfa15023 //
    private static final String APIKEY = /*
     * "2f102b6aceecd97fb83b1fc6dfa15023"
     */ "2f102b6aceecd97fb83b1fc6dfa15023";

    private final WebTarget pwt;
    private final WebTarget hwt;
    private final WebTarget cwt;
    private final WebTarget gwt;
/*
    Standard Lookup: Look up the data behind an IP address.
    Bulk Lookup: Look up the data behind multiple IP addresses at once.
    Requester Lookup: Look up the data behind the IP address your API request is coming from.
*/
    /*
components

        components.co Сoncentration of CO (Carbon monoxide), μg/m3
        components.no Сoncentration of NO (Nitrogen monoxide), μg/m3
        components.no2 Сoncentration of NO2 (Nitrogen dioxide), μg/m3
        components.o3 Сoncentration of O3 (Ozone), μg/m3
        components.so2 Сoncentration of SO2 (Sulphur dioxide), μg/m3
        components.pm2_5 Сoncentration of PM2.5 (Fine particles matter), μg/m3
        components.pm10 Сoncentration of PM10 (Coarse particulate matter), μg/m3
        components.nh3 Сoncentration of NH3 (Ammonia), μg/m3
*/

    public PollutionData(){
        final Client client = ClientBuilder.newClient();
        pwt = client.target(POL_URL);
        hwt = client.target(HIS_URL);
        cwt = client.target(CIT_URL);
        gwt = client.target(GEO_URL);
    }
    @GET
    @Produces(MediaType.APPLICATION_JSON)
    @Path("/actualPollutionIn")
    public JsonObject getPollutionDataByName(@QueryParam("cityName") String city){
        //get lat and lon from our WS
//        final Response response = hwt.queryParam("cityname",city).request(MediaType.APPLICATION_JSON).get();
//        final JsonObject jsonObject = Json.createReader(response.readEntity(InputStream.class)).readObject();
        //final JsonObject componentData = jsonObject.getJsonArray("list").getJsonObject(0).getJsonObject("components");

        //get from GEO
        final Response response = gwt.queryParam("q",city).queryParam("limit",1).queryParam("appid", APIKEY).request(MediaType.APPLICATION_JSON).get();
        final JsonArray jsonArray = Json.createReader(response.readEntity(InputStream.class)).readArray();
        float lat = (float) jsonArray.getJsonObject(0).getJsonNumber("lat").doubleValue();
        float lon = (float) jsonArray.getJsonObject(0).getJsonNumber("lon").doubleValue();
        return getPollutionData(lat, lon);
    }

    @GET
    @Produces(MediaType.APPLICATION_JSON)
    @Path("/HistoryPollutionIn")
    public JsonObject getHistoryPollutionDataByName(@QueryParam("cityName") String city,@QueryParam("startDate") int start,@QueryParam("endDate") int end){
        //get lat and lon from our WS
        //float lat, lon;

        //get from GEO
        final Response response = gwt.queryParam("q",city).queryParam("limit",1).queryParam("appid", APIKEY).request(MediaType.APPLICATION_JSON).get();
        final JsonArray jsonArray = Json.createReader(response.readEntity(InputStream.class)).readArray();
        float lat = (float) jsonArray.getJsonObject(0).getJsonNumber("lat").doubleValue();
        float lon = (float) jsonArray.getJsonObject(0).getJsonNumber("lon").doubleValue();
        return getHistoryPollutionData(lat, lon, start, end);
    }

    @Produces(MediaType.APPLICATION_JSON)
    public JsonObject getHistoryPollutionData(float lat, float lon, int start, int end) {

        final Response response = hwt.queryParam("lat",lat).queryParam("lon",lon).queryParam("start",start).queryParam("end",end).queryParam("appid", APIKEY).request(MediaType.APPLICATION_JSON).get();
        final JsonObject jsonObject = Json.createReader(response.readEntity(InputStream.class)).readObject();
        final JsonObject componentData = jsonObject.getJsonArray("list").getJsonObject(0).getJsonObject("components");
        //Rückgabe prüfen
        return componentData;
    }


    @Produces(MediaType.APPLICATION_JSON)
    public JsonObject getPollutionData(float lat, float lon) {

        final Response response = pwt.queryParam("lat",lat).queryParam("lon",lon).queryParam("appid", APIKEY).request(MediaType.APPLICATION_JSON).get();
        final JsonObject jsonObject = Json.createReader(response.readEntity(InputStream.class)).readObject();
        final JsonObject componentData = jsonObject.getJsonArray("list").getJsonObject(0).getJsonObject("components");
        return componentData;
    }
}


