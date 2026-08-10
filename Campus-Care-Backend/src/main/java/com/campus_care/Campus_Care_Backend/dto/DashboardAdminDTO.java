package com.campus_care.Campus_Care_Backend.dto;

import com.fasterxml.jackson.annotation.JsonProperty;

import java.util.List;

public class DashboardAdminDTO {

    private Long totalUsuarios;
    private Long evaluacionesHoy;
    private Double riesgoPromedio;
    private String riesgoCategoria;

    private Long totalEstudiantes;

    @JsonProperty("avg_estres")
    private double avgEstres;
    @JsonProperty("avg_ansiedad")
    private double avgAnsiedad;
    @JsonProperty("avg_sueno")
    private double avgSueno;
    @JsonProperty("avg_estado_animo")
    private double avgEstadoAnimo;
    @JsonProperty("avg_relaciones")
    private double avgRelaciones;
    @JsonProperty("avg_motivacion")
    private double avgMotivacion;


    public Long getTotalEstudiantes(){
        return totalEstudiantes;
    }

    public void setTotalEstudiantes(Long totalEstudiantes){
        this.totalEstudiantes = totalEstudiantes;
    }

    public Long getTotalUsuarios() {
        return totalUsuarios;
    }

    public void setTotalUsuarios(Long totalUsuarios) {
        this.totalUsuarios = totalUsuarios;
    }

    public Long getEvaluacionesHoy() {
        return evaluacionesHoy;
    }

    public void setEvaluacionesHoy(Long evaluacionesHoy) {
        this.evaluacionesHoy = evaluacionesHoy;
    }

    public Double getRiesgoPromedio() {
        return riesgoPromedio;
    }

    public void setRiesgoPromedio(Double riesgoPromedio) {
        this.riesgoPromedio = riesgoPromedio;
    }

    public String getRiesgoCategoria() {
        return riesgoCategoria;
    }

    public void setRiesgoCategoria(String riesgoCategoria) {
        this.riesgoCategoria = riesgoCategoria;
    }

    public double getAvgEstres() {
        return avgEstres;
    }

    public void setAvgEstres(double avgEstres) {
        this.avgEstres = avgEstres;
    }

    public double getAvgAnsiedad() {
        return avgAnsiedad;
    }

    public void setAvgAnsiedad(double avgAnsiedad) {
        this.avgAnsiedad = avgAnsiedad;
    }

    public double getAvgSueno() {
        return avgSueno;
    }

    public void setAvgSueno(double avgSueno) {
        this.avgSueno = avgSueno;
    }

    public double getAvgEstadoAnimo() {
        return avgEstadoAnimo;
    }

    public void setAvgEstadoAnimo(double avgEstadoAnimo) {
        this.avgEstadoAnimo = avgEstadoAnimo;
    }

    public double getAvgRelaciones() {
        return avgRelaciones;
    }

    public void setAvgRelaciones(double avgRelaciones) {
        this.avgRelaciones = avgRelaciones;
    }

    public double getAvgMotivacion() {
        return avgMotivacion;
    }

    public void setAvgMotivacion(double avgMotivacion) {
        this.avgMotivacion = avgMotivacion;
    }
}
