package DreiKoKb.Service;

import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import java.io.BufferedReader;
import java.io.FileReader;
import java.io.InputStream;
import java.io.InputStreamReader;

import org.json.JSONObject;

@Path("/cityconvert")
public class CityConverter {
    public final static String fullCityChangerFile = "/../../../resources/main/citylist/cityfull.txt";
    public final static String germanCityChangerFile = "/../../../resources/main/citylist/citygerman.txt";

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
    @Path("/lat={lati}&lon={longi}")
    @Produces(MediaType.APPLICATION_JSON)
    public String nearestCity(@PathParam("lati") double lati, @PathParam("longi") double longi){
        StringBuilder response = new StringBuilder();
        try{
            BufferedReader myReader = new BufferedReader(new FileReader(germanCityChangerFile));
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
            return e.getMessage();
        }
        return  (response.toString());
    }

    @Path("/coolcityname={city}")
    @GET
    @Produces(MediaType.APPLICATION_JSON)
    public Response cityCool(@PathParam("city") String city){
        StringBuilder response = new StringBuilder();
        try{
            InputStream in = getClass().getResourceAsStream("/cityList/cityfull.txt");
            BufferedReader myReader = new BufferedReader(new InputStreamReader(in));
            myReader.mark(41700915);
            int lineCounter = 0;
            String line;
            while((line = myReader.readLine())!= null){
                line = line.toLowerCase();
                if(line.contains("\""+city.toLowerCase()+"\"")) break;
                lineCounter++;
            }

            myReader.reset();

            for(int i = 0; i < lineCounter-2; i++)
                myReader.readLine();



            for(int i = 0; i < 10; i++){
                response.append(myReader.readLine());
            }
            JSONObject object = new JSONObject(response.toString());
            return Response.ok(object.toString()).build();
        }catch (Exception e){
            System.out.println(e.getMessage());
            System.out.println(System.getProperty("user.dir"));
            response = new StringBuilder(
                    "{\n" + "\"id\": -1,\n" + "\"name\": \"-1\",\n" + "\"state\": \"-1\",\n" + "\"country\": \"-1\",\n" + "\"coord\": {\n" + "\"lon\": -1.0,\n" + "\"lat\": -1.0\n" + "}\n" + "}"
            );
            JSONObject object = new JSONObject(response.toString());
            return Response.ok(object.toString()).build();
        }

    }
}
