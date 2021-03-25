package DreiKoKb.Model;

import org.json.JSONObject;

public class City {
    private String cityName;
    private long id;
    private Coord coord;
    private String country;

    public City(String input){
        try{
            coord = new Coord();
            JSONObject object = new JSONObject(input.substring(0,input.length()-1));
            id = object.getLong("id");
            cityName = object.getString("name");
            coord.setLat(object.getJSONObject("coord").getDouble("lat"));
            coord.setLon(object.getJSONObject("coord").getDouble("lon"));
            country = object.getString("country");
            System.out.println(input.substring(0,input.length()-1));
        }
        catch (Exception e){
            cityName = e.getMessage();
            country = input;
            id =-1;
            coord.setLon(-1);
            coord.setLat(-1);
        }

    }

    public String getCityName() {
        return cityName;
    }

    public long getId() {
        return id;
    }

    public Coord getCoord() {
        return coord;
    }

    public String getCountry() {
        return country;
    }
}
