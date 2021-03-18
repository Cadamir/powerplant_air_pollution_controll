package DreiKoKb.Service;

import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import java.io.File;
import java.util.Locale;
import java.util.Scanner;

@Path("/cityconvert")
public class CityConverter {
    public final static String fullCityChangerFile = "C:/Users/User/Documents/GitHub/powerplant_air_pollution_controll/city_converter/cityList/city.list.json.txt";
    //public final static String germanCityChangerFile;

    @Path("/version")
    @GET
    @Produces(MediaType.TEXT_PLAIN)
    public String version(){
        return "version";
    }

    @Path("/cityname={city}")
    @GET
    @Produces(MediaType.TEXT_PLAIN)
    public String city(@PathParam("city") String city){
        try{
            String response = "";
            File file = new File(fullCityChangerFile);
            Scanner myReader = new Scanner(file);
            return myReader.nextLine();/*
            int lineCounter = 0;
            String line;
            while(myReader.hasNextLine()){
                line = myReader.nextLine().toLowerCase(Locale.ROOT);
                response = line;
                if(line.contains("\""+city+"\"")) break;
                lineCounter++;
            }

            myReader.reset();

            for(int i = 0; i < lineCounter-2; i++)
                myReader.nextLine();



            for(int i = 0; i < 9; i++){
                response = response + myReader.nextLine();
            }
            return response;*/
        }catch (Exception e){
            return e.getMessage();
        }
    }
}
