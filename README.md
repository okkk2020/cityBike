# Bike-sharing application
This application
* Fetches and processes the following data:
  * Stations names
  * Stations coordinates (latitude and longitude)
  * The number of free bikes available at the stations
* Parses a CSV file that contains:
  * The count of bikers in need of a bike;
  * The coordinates (latitude and longitude) at which the bikes are located.  Example file structure:
```
count, latitude, longitude
2, 45.69233, 9.65931
1, 45.69654, 9.65897
0, 45.67831, 9.67516
4, 45.716909, 9.716649
```
* This example displays the closest bike station and the bikes available in Bergamo, Italy:
```
php app/main.php Bergamo

distance: 0.024340748060035
name: 19. Palma il Vecchio
free_bike_count: 0
biker_count: 2

distance: 0.096520693247684
name: 23. Mazzini
free_bike_count: 0
biker_count: 1

distance: 0.36324024061141
name: VIA AMBIVERI
free_bike_count: 0
biker_count: 0

distance: 1.2777018248149
name: VIA CORRIDONI - MARTINELLA
free_bike_count: 0
biker_count: 4
```
In order to install the application please execute 'composer install' and that will suffice.
 
