import { React, useState, useEffect } from 'react'
import axios from 'axios';

const Weather = () => {
    const [weather, setWeather] = useState(null);
    const url = "http://api.weatherapi.com/v1/current.json";

    useEffect(() => {
        axios.get(url, {
            params: {
                "key": process.env.REACT_APP_API_KEY,
                "q": "Belgrade",
                "aqi": "no"
            }
        }).then(response => {
            console.log(response);
            setWeather({
                temp: response.data.current.temp_c,
                condition: response.data.current.condition.text,
                weatherImg: response.data.current.condition.icon
            });
        }
        ).catch(error => {
            console.error('Error while getting weather!:', error);
        })
        
    }, []);

  return (
    <>
        {weather==null ? <></> :
        <div style={{ display: "flex", alignItems: "center", 
                  color: "#d8caf9", fontFamily: "serif", fontStyle: "italic", fontSize: "1.3rem"}}>
                  <p style={{ marginBottom: 0 }}>{weather.temp}°C {weather.condition}</p>
            <img src={weather.weatherImg} alt='Weather img'></img>
         </div>
            } 
    </>
  )
}

export default Weather