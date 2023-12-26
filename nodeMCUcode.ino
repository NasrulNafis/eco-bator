#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <DHT.h>

#define lampPin D1
#define humdPin D2
#define ExFanPin D3
#define InFanPin D4
#define DHTPIN D5 // DHT11 data pin
#define DHTTYPE DHT11 // DHT11 sensor type


DHT dht(DHTPIN, DHTTYPE);
//enter WIFI credentials
const char* ssid     = "";
const char* password = "";
//enter domain name and path
const char* SERVER_NAME = "";
const char* host = "";

//PROJECT_API_KEY is the exact duplicate of, PROJECT_API_KEY in config.php file
//Both values must be same
String PROJECT_API_KEY = "egginc";
//-------------------------------------------------------------------
//Send an HTTP POST request every 30 seconds
unsigned long lastMillis = 0;
long interval = 5000;

void setup() {
  Serial.begin(115200);
  dht.begin();
  pinMode(InFanPin, OUTPUT);
  pinMode(ExFanPin, OUTPUT);
  pinMode(lampPin, OUTPUT);
  pinMode(humdPin, OUTPUT);

  digitalWrite(InFanPin, LOW);
  digitalWrite(ExFanPin, LOW);
  digitalWrite(lampPin, LOW);
  digitalWrite(humdPin, LOW);


  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
   if(WiFi.status()== WL_CONNECTED){
    if(millis() - lastMillis > interval) {
       //Send an HTTP POST request every interval seconds
       lastMillis = millis();
    }
  }
  //-----------------------------------------------------------------
  else {
    Serial.println("WiFi Disconnected");
  }
  
  upload_temperature();
  
HTTPClient http; //--> Declare object of class HTTPClient

  //----------------------------------------Getting Data from MySQL Database
  String GetAddress, LinkGet, getData;
  int id = 0; //--> ID in Database
  
  float humd = dht.readHumidity();
  float temp = dht.readTemperature();
  if (isnan(humd) || isnan(temp)) {
    Serial.println("Failed to read from DHT sensor!");
    return;
  }
  
  GetAddress = "/getmanual.php"; 
  LinkGet = host + GetAddress; //--> Make a Specify request destination
  getData = "ID=" + String(id);
  Serial.println("----------------Connect to Server-----------------");
  Serial.println("Get OVERRIDE Status from Database");
  Serial.print("Request Link : ");
  Serial.println(LinkGet);
  http.begin(LinkGet); //--> Specify request destination
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");    //Specify content-type header
  int httpCodeGet = http.POST(getData); //--> Send the request
  String GetControl = http.getString(); //--> Get the response payload from server
  Serial.print("Response Code : "); //--> If Response Code = 200 means Successful connection, if -1 means connection failed. 
  Serial.println(httpCodeGet); //--> Print HTTP return code
  Serial.print("Returned data from Server : ");
  Serial.println(GetControl); //--> Print request response payload

  Serial.print("temperature : ");
  Serial.println(temp);
  Serial.print(" Humidity : ");
  Serial.println(humd);
  Serial.println("");

  if (GetControl == "0") 
    {
      if (temp > 35){
        digitalWrite(InFanPin, HIGH);
        digitalWrite(ExFanPin, HIGH);
        digitalWrite(lampPin, LOW);
        digitalWrite(humdPin, HIGH);
        Serial.print("!!Incubator HOT!! ");
      }
      if (temp <= 34){
       digitalWrite(InFanPin, LOW);
       digitalWrite(ExFanPin, LOW);
       digitalWrite(lampPin, HIGH);
       digitalWrite(humdPin, LOW);
       Serial.print("!!Incubator COLD!! ");
      }
  }
  if (GetControl == "1"){
      getInFan();
      getExFan();
      getLamp();
      getHumid();
  }
  Serial.println("");
  Serial.println("----------------Closing Connection----------------");
  
  http.end(); //--> Close connection
  Serial.println();
  Serial.println();


  delay(10000);
}

void getInFan(){
HTTPClient http; //--> Declare object of class HTTPClient

  //----------------------------------------Getting Data from MySQL Database
  String GetAddress, LinkGet, getData;
  int id = 0; //--> ID in Database

  GetAddress = "/getinfan.php"; 
  LinkGet = host + GetAddress; //--> Make a Specify request destination
  getData = "ID=" + String(id);
  Serial.println("----------------Connect to Server-----------------");
  Serial.println("Get INLET FAN Status from Database");
  Serial.print("Request Link : ");
  Serial.println(LinkGet);
  http.begin(LinkGet); //--> Specify request destination
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");    //Specify content-type header
  int httpCodeGet = http.POST(getData); //--> Send the request
  String GetInFan = http.getString(); //--> Get the response payload from server
  Serial.print("Response Code : "); //--> If Response Code = 200 means Successful connection, if -1 means connection failed. 
  Serial.println(httpCodeGet); //--> Print HTTP return code
  Serial.print("Inlet Fan Status : ");
  Serial.println(GetInFan); //--> Print request response payload

  if (GetInFan == "1") {
    digitalWrite(InFanPin, HIGH); //--> Turn off Led
  }
  if (GetInFan == "0") {
    digitalWrite(InFanPin, LOW); //--> Turn off Led
  }
 
  Serial.println("----------------Closing Connection----------------");
  
  http.end(); //--> Close connection
  Serial.println();
}

void getExFan(){
HTTPClient http; //--> Declare object of class HTTPClient

  //----------------------------------------Getting Data from MySQL Database
  String GetAddress, LinkGet, getData;
  int id = 0; //--> ID in Database

  GetAddress = "/getexfan.php"; 
  LinkGet = host + GetAddress; //--> Make a Specify request destination
  getData = "ID=" + String(id);
  Serial.println("----------------Connect to Server-----------------");
  Serial.println("Get EX FAN Status from Database");
  Serial.print("Request Link : ");
  Serial.println(LinkGet);
  http.begin(LinkGet); //--> Specify request destination
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");    //Specify content-type header
  int httpCodeGet = http.POST(getData); //--> Send the request
  String GetExFan = http.getString(); //--> Get the response payload from server
  Serial.print("Response Code : "); //--> If Response Code = 200 means Successful connection, if -1 means connection failed. 
  Serial.println(httpCodeGet); //--> Print HTTP return code
  Serial.print("Exhaust Fan Status : ");
  Serial.println(GetExFan); //--> Print request response payload

  if (GetExFan == "1") {
    digitalWrite(ExFanPin, HIGH); //--> Turn off Led
  }
  if (GetExFan == "0") {
    digitalWrite(ExFanPin, LOW); //--> Turn off Led
  }
 
  Serial.println("----------------Closing Connection----------------");
  
  http.end(); //--> Close connection
  Serial.println();
 
}

void getLamp(){
HTTPClient http; //--> Declare object of class HTTPClient

  //----------------------------------------Getting Data from MySQL Database
  String GetAddress, LinkGet, getData;
  int id = 0; //--> ID in Database

  GetAddress = "/getlamp.php"; 
  LinkGet = host + GetAddress; //--> Make a Specify request destination
  getData = "ID=" + String(id);
  Serial.println("----------------Connect to Server-----------------");
  Serial.println("Get LAMP Status from Database");
  Serial.print("Request Link : ");
  Serial.println(LinkGet);
  http.begin(LinkGet); //--> Specify request destination
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");    //Specify content-type header
  int httpCodeGet = http.POST(getData); //--> Send the request
  String GetLamp = http.getString(); //--> Get the response payload from server
  Serial.print("Response Code : "); //--> If Response Code = 200 means Successful connection, if -1 means connection failed. 
  Serial.println(httpCodeGet); //--> Print HTTP return code
  Serial.print("Lamp status : ");
  Serial.println(GetLamp); //--> Print request response payload

  if (GetLamp == "1") {
    digitalWrite(lampPin, HIGH); //--> Turn off Led
  }
  if (GetLamp == "0") {
    digitalWrite(lampPin, LOW); //--> Turn off Led
  }
 
  Serial.println("----------------Closing Connection----------------");
  
  http.end(); //--> Close connection
  Serial.println();
}

void getHumid(){
HTTPClient http; //--> Declare object of class HTTPClient

  //----------------------------------------Getting Data from MySQL Database
  String GetAddress, LinkGet, getData;
  int id = 0; //--> ID in Database

  GetAddress = "/gethumid.php"; 
  LinkGet = host + GetAddress; //--> Make a Specify request destination
  getData = "ID=" + String(id);
  Serial.println("----------------Connect to Server-----------------");
  Serial.println("Get HUMIDIFIER Status from Database");
  Serial.print("Request Link : ");
  Serial.println(LinkGet);
  http.begin(LinkGet); //--> Specify request destination
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");    //Specify content-type header
  int httpCodeGet = http.POST(getData); //--> Send the request
  String GetHumid = http.getString(); //--> Get the response payload from server
  Serial.print("Response Code : "); //--> If Response Code = 200 means Successful connection, if -1 means connection failed. 
  Serial.println(httpCodeGet); //--> Print HTTP return code
  Serial.print("Humidifier status : ");
  Serial.println(GetHumid); //--> Print request response payload

  if (GetHumid == "1") {
    digitalWrite(humdPin, HIGH); //--> Turn off Led
  }
  if (GetHumid == "0") {
    digitalWrite(humdPin, LOW); //--> Turn off Led
  }
 
  Serial.println("----------------Closing Connection----------------");
  
  http.end(); //--> Close connection
  Serial.println(); 
}

void upload_temperature()
{
  float temp = dht.readTemperature();
  float humd= dht.readHumidity();
  
  String humidity = String(humd, 2);
  String temperature = String(temp, 2);

  //HTTP POST request data
  String temperature_data;
  temperature_data = "api_key="+PROJECT_API_KEY;
  temperature_data += "&temperature="+temperature;
  temperature_data += "&humidity="+humidity;

  Serial.print("temperature_data: ");
  Serial.println(temperature_data);
  //--------------------------------------------------------------------------------
  
  WiFiClient client;
  HTTPClient http;

  http.begin(SERVER_NAME);
  // Specify content-type header
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  // Send HTTP POST request
  int httpResponseCode = http.POST(temperature_data);

  Serial.print("HTTP Response code: ");
  Serial.println(httpResponseCode);
   
  // Free resources
  http.end();
  }
