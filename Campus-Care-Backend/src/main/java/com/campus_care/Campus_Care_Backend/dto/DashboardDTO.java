package com.campus_care.Campus_Care_Backend.dto;

import java.util.List;

public class DashboardDTO {

    private List<String> fechas;

    private List<MetricaSerieDTO> series;

    private int rachaActual;

    private int puntosTotales;

    private String ultimaAutoevaluacionId;

    public String getUltimaAutoevaluacionId(){
        return ultimaAutoevaluacionId;
    }

    public void setUltimaAutoevaluacionId(String ultimaAutoevaluacionId){
        this.ultimaAutoevaluacionId = ultimaAutoevaluacionId;
    }

    public int getRachaActual(){
        return rachaActual;
    }

    public void setRachaActual(int rachaActual){
        this.rachaActual = rachaActual;
    }

    public int getPuntosTotales(){
        return puntosTotales;
    }

    public void setPuntosTotales(int puntosTotales){
        this.puntosTotales = puntosTotales;
    }

    public List<String> getFechas() {
        return fechas;
    }

    public void setFechas(List<String> fechas) {
        this.fechas = fechas;
    }

    public List<MetricaSerieDTO> getSeries() {
        return series;
    }

    public void setSeries(List<MetricaSerieDTO> series) {
        this.series = series;
    }
}
