package DreiKoKb.Model;

import org.json.JSONObject;

public class City {
    private String cityName;
    private long id;
    private double lat;
    private double lon;
    private String country;

    public City(String input){
        try{
            JSONObject object = new JSONObject(input);
            id = object.getLong("id");
            cityName = object.getString("name");
            lat = object.getJSONObject("coord").getDouble("lat");
            lon = object.getJSONObject("coord").getDouble("lon");
            country = object.getString("country");
        }catch (Exception e){
            id = -1;
            cityName = e.getMessage();
            country = input;
            lat = -1;
            lon = -1;
        }

    }

    public String getCityName() {
        return cityName;
    }

    public long getId() {
        return id;
    }

    public double getLat() {
        return lat;
    }

    public double getLon() {
        return lon;
    }

    public String getCountry() {
        return country;
    }
}
