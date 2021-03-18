package DreiKoKb.Model;

import org.json.JSONObject;

public class City {
    private String cityName;
    private long id;
    private double lat;
    private double lon;

    public City(String input){
        JSONObject object = new JSONObject(input);
        id = object.getLong("id");
        cityName = object.getString("name");
        lat = object.getJSONObject("coord").getDouble("lat");
        lon = object.getJSONObject("coord").getDouble("lon");
    }
}
