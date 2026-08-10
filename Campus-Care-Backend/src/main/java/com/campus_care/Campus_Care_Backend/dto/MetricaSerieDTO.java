package com.campus_care.Campus_Care_Backend.dto;

import java.util.List;

public class MetricaSerieDTO {

    private String metrica;         // "ESTRES", "SUENO"
    private List<Integer> valores;

    public String getMetrica(){
        return metrica;
    }

    public void setMetrica(String metrica){
        this.metrica = metrica;
    }

    public List<Integer> getValores(){
        return valores;
    }

    public void setValores(List<Integer> valores){
        this.valores = valores;
    }

}
