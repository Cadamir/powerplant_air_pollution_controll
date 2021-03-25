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
import org.jboss.resteasy.client.jaxrs.ResteasyClient;
import org.jboss.resteasy.client.jaxrs.ResteasyClientBuilder;
import org.jboss.resteasy.client.jaxrs.internal.ResteasyClientBuilderImpl;
import org.jboss.resteasy.client.jaxrs.ResteasyWebTarget;

@Path("/pol")
public class PollutionData {

    private static final String POL_URL = /*
     * "http://api.openweathermap.org/data/2.5/air_pollution"
     */ "http://api.openweathermap.org/data/2.5/air_pollution";

    private static final String HIS_URL = /*
     * "http://api.openweathermap.org/data/2.5/air_pollution/history"
     */ "http://api.openweathermap.org/data/2.5/air_pollution/history";

    private static final String CIT_URL = /*
     * "http://localhost:7353/cityconvert/city"
     */ "http://10.50.15.51:7353/cityconvert/city";

    private static final String COO_URL = /*
     * "http://localhost:7353/cityconvert/city"
     */ "http://10.50.15.51:7353/cityconvert/coord";


    // APIKEY: 2f102b6aceecd97fb83b1fc6dfa15023 //
    private static final String APIKEY = /*
     * "2f102b6aceecd97fb83b1fc6dfa15023"
     */ "2f102b6aceecd97fb83b1fc6dfa15023";

    private final WebTarget pwt; //actual Pollutiondata from OpenWeather
    private final WebTarget hwt; //history Pollutiondata from OpenWeather
    private final ResteasyWebTarget iwt; //coords by Name from our WS
    private final ResteasyWebTarget owt; //coords by coords from our ws

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
        final ResteasyClient rClient = new ResteasyClientBuilderImpl().build();
        pwt = client.target(POL_URL);
        hwt = client.target(HIS_URL);
        iwt = rClient.target(CIT_URL);
        owt = rClient.target(COO_URL);
    }
    @GET
    @Produces(MediaType.APPLICATION_JSON)
    @Path("/actualPollutionIn")
    public String getPollutionDataByName(@QueryParam("cityName") String city){
        //get lat and lon from our WS
        final Response response = iwt.queryParam("cityName",city).request(MediaType.APPLICATION_JSON).get();
        final JsonObject jsonObject = Json.createReader(response.readEntity(InputStream.class)).readObject();
        float lat = (float) jsonObject.getJsonObject("coord").getJsonNumber("lat").doubleValue();
        float lon = (float) jsonObject.getJsonObject("coord").getJsonNumber("lon").doubleValue();
        if(response != null){
            response.close(); //close connection
        }
        //get and return pollutiondata
        return getPollutionData(lat, lon).toString();
    }

    @GET
    @Produces(MediaType.APPLICATION_JSON)
    @Path("/HistoryPollutionIn")
    public String getHistoryPollutionDataByName(@QueryParam("cityName") String city,@QueryParam("startDate") int start,@QueryParam("endDate") int end){
        //get lat and lon from our WS
        final Response response = iwt.queryParam("cityName",city).request(MediaType.APPLICATION_JSON).get();
        final JsonObject jsonObject = Json.createReader(response.readEntity(InputStream.class)).readObject();
        float lat = (float) jsonObject.getJsonObject("coord").getJsonNumber("lat").doubleValue();
        float lon = (float) jsonObject.getJsonObject("coord").getJsonNumber("lon").doubleValue();
        if(response != null){
            response.close(); //close connection
        }
        //get and return pollutiondata
        return getHistoryPollutionData(lat, lon, start, end).toString();
    }

    @GET
    @Produces(MediaType.APPLICATION_JSON)
    @Path("/actualPollutionCoord")
    public String getPollutionDataByName(@QueryParam("lat") float lat, @QueryParam("lon") float lon){
        //get correct Coords from WS for OpenWeatherMap
        final Response response = owt.queryParam("lat",lat).queryParam("lon",lon).request(MediaType.APPLICATION_JSON).get();
        final JsonObject jsonObject = Json.createReader(response.readEntity(InputStream.class)).readObject();
        lat = (float) jsonObject.getJsonObject("coord").getJsonNumber("lat").doubleValue();
        lon = (float) jsonObject.getJsonObject("coord").getJsonNumber("lon").doubleValue();
        if(response != null){
            response.close(); //close connection
        }
        //get and return pollutiondata
        return getPollutionData(lat, lon).toString();
    }

    @GET
    @Produces(MediaType.APPLICATION_JSON)
    @Path("/HistoryPollutionCoord")
    public String getHistoryPollutionDataByName(@QueryParam("lat") float lat, @QueryParam("lon") float lon,@QueryParam("startDate") int start,@QueryParam("endDate") int end){
        //get correct Coords from WS for OpenWeatherMap
        final Response response = owt.queryParam("lat",lat).queryParam("lon",lon).request(MediaType.APPLICATION_JSON).get();
        final JsonObject jsonObject = Json.createReader(response.readEntity(InputStream.class)).readObject();
        lat = (float) jsonObject.getJsonObject("coord").getJsonNumber("lat").doubleValue();
        lon = (float) jsonObject.getJsonObject("coord").getJsonNumber("lon").doubleValue();
        if(response != null){
            response.close(); //close connection
        }
        //get and return pollutiondata
        return getHistoryPollutionData(lat, lon, start, end).toString();
    }

    @Produces(MediaType.APPLICATION_JSON)
    public JsonArray getHistoryPollutionData(float lat, float lon, int start, int end) {
        //get Data from OpenWeatherMap
        final Response response = hwt.queryParam("lat",lat).queryParam("lon",lon).queryParam("start",start).queryParam("end",end).queryParam("appid", APIKEY).request(MediaType.APPLICATION_JSON).get();
        final JsonObject jsonObject = Json.createReader(response.readEntity(InputStream.class)).readObject();
        final JsonArray componentData = jsonObject.getJsonArray("list");
        if(response != null){
            response.close(); //close connection
        }
        //return pollutiondata
        return componentData;
    }


    @Produces(MediaType.APPLICATION_JSON)
    public JsonArray getPollutionData(float lat, float lon) {
        //get Data from OpenWeatherMap
        final Response response = pwt.queryParam("lat",lat).queryParam("lon",lon).queryParam("appid", APIKEY).request(MediaType.APPLICATION_JSON).get();
        final JsonObject jsonObject = Json.createReader(response.readEntity(InputStream.class)).readObject();
        final JsonArray componentData = jsonObject.getJsonArray("list");
        if(response != null){
            response.close(); //close connection
        }
        //return pollutiondata
        return componentData;
    }
}



