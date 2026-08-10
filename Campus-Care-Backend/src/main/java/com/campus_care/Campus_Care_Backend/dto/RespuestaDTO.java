package com.campus_care.Campus_Care_Backend.dto;

public class RespuestaDTO {

    private String metrica;
    private Integer score;

    public String getMetrica(){
        return metrica;
    }

    public void setMetrica(String metrica){
        this.metrica = metrica;
    }

    public Integer getScore(){
        return score;
    }

    public void setScore(Integer score){
        this.score = score;
    }

}
