package DreiKoKb.Service;

import javax.ws.rs.*;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import java.io.BufferedReader;
import java.io.FileReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.nio.charset.StandardCharsets;
import java.util.Locale;

import DreiKoKb.Model.City;
import org.json.JSONObject;

@Path("/cityconvert")
public class CityConverter {
    public final static String fullCityChangerFile = "/cityList/cityfull.txt";
    public final static String germanCityChangerFile = "/cityList/citygerman.txt";

    @Path("/version")
    @GET
    @Produces(MediaType.TEXT_PLAIN)
    public String version(){
        return "version";
    }

    @Path("/legacycityname={city}")
    @GET
    @Produces(MediaType.TEXT_PLAIN)
    public JSONObject city(@PathParam("city") String city){
        StringBuilder response = new StringBuilder();
        try{
            BufferedReader myReader = new BufferedReader(new FileReader(System.getProperty("user.dir") + fullCityChangerFile));
            myReader.mark(41700915);
            int lineCounter = 0;
            String line;
            while((line = myReader.readLine())!= null){
                line = line.toLowerCase();
                if(line.contains("\""+city+"\"")) break;
                lineCounter++;
            }

            myReader.reset();

            for(int i = 0; i < lineCounter-2; i++)
                myReader.readLine();



            for(int i = 0; i < 10; i++){
                response.append(myReader.readLine());
            }

        }catch (Exception e){
            System.out.println(e.getMessage());
            System.out.println(System.getProperty("user.dir"));
            response = new StringBuilder("{\"empty\": \"true\"}");
        }
       return new JSONObject(response.toString());
    }

    @GET
    @Path("/coord")
    @Produces(MediaType.APPLICATION_JSON)
    public Response nearestCity(@QueryParam("lat") double lati, @QueryParam("lon") double longi){
        StringBuilder response = new StringBuilder();
        try{
            InputStream in = getClass().getResourceAsStream(germanCityChangerFile);
            BufferedReader myReader = new BufferedReader(new InputStreamReader(in));
            myReader.mark(41700915);
            String line;
            String lastLine = "";
            int lineCounter = 0;
            int goodLine = 1;
            double closestDistance = 9999999;
            while((line = myReader.readLine())!=null){
                if(line.contains("\"lat\":") && lastLine.contains("\"lon\":")){

                    double lat = Double.parseDouble(line.replace("\"lat\": ", "").replace(",", ""));
                    double lon = Double.parseDouble(lastLine.replace("\"lon\":", "").replace(",", ""));
                    double distance = Math.sqrt((lati-lat)*(lati-lat)+(longi-lon)*(longi-lon));

                    if(distance < closestDistance){
                        goodLine = lineCounter - 7;
                        closestDistance = distance;

                    }

                }

                lineCounter++;
                lastLine = line;
            }
            myReader.reset();
            for(int i = 0; i < goodLine; i++)
                myReader.readLine();
            for(int i = 0; i < 10; i++){
                response.append(myReader.readLine());
            }
        }catch (Exception e){
            response.append(e.getMessage());
        }
        JSONObject o =  new JSONObject(new City(response.toString()));
        return Response.ok(o.toString()).build();
    }

    @Path("/city")
    @GET
    @Produces(MediaType.APPLICATION_JSON)
    public Response cityCool(@QueryParam("cityName") String city){
        StringBuilder response = new StringBuilder();
        try{
            InputStream in = getClass().getResourceAsStream(fullCityChangerFile);
            BufferedReader myReader = new BufferedReader(new InputStreamReader(in, StandardCharsets.UTF_8));
            myReader.mark(41700915);
            int lineCounter = 0;
            String line, lastLine = "";
            while((line = myReader.readLine())!= null){
                line = line.toLowerCase();
                if(line.toLowerCase(Locale.ROOT).contains("nchen\"")) System.out.println("Line: " + line);
                if(line.toLowerCase().contains("\""+city.toLowerCase()+"\"")) break;
                lineCounter++;
                lastLine = line;
            }
            System.out.println("LastLine: "+ lastLine);
            myReader.reset();

            for(int i = 0; i < lineCounter-2; i++)
                myReader.readLine();

            for(int i = 0; i < 10; i++){
                response.append(myReader.readLine());
            }
            System.out.println("Response: "+response.toString());
            JSONObject object = new JSONObject(new City(response.toString()));
            return Response.ok(object.toString()).build();
        }catch (Exception e){
            System.out.println(e.getMessage());
            System.out.println(System.getProperty("user.dir"));
            response = new StringBuilder(
                    "{\n" + "\"id\": -1,\n" + "\"name\": \"-1\",\n" + "\"state\": \"-1\",\n" + "\"country\": \"-1\",\n" + "\"coord\": {\n" + "\"lon\": -1.0,\n" + "\"lat\": -1.0\n" + "}\n" + "}"
            );
            JSONObject object = new JSONObject(new City(response.toString()));
            return Response.ok(object.toString()).build();
        }

    }

    @GET
    @Path("/test")
    @Produces(MediaType.TEXT_PLAIN)
    public Response test(@QueryParam("input") String input){
        return Response.ok(input.toLowerCase()).build();
    }
}
