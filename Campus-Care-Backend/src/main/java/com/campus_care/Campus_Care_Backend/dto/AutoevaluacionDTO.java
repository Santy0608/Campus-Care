package com.campus_care.Campus_Care_Backend.dto;

import org.springframework.data.mongodb.core.aggregation.ArrayOperators;

import java.time.Instant;
import java.util.List;

public class AutoevaluacionDTO {

    private String id;
    private String idUsuario;
    private Instant fechaEvaluacion;
    private List<RespuestaDTO> respuestas;
    private Integer puntosGanados;

    public Integer getPuntosGanados(){
        return puntosGanados;
    }

    public void setPuntosGanados(Integer puntosGanados){
        this.puntosGanados = puntosGanados;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getIdUsuario() {
        return idUsuario;
    }

    public void setIdUsuario(String idUsuario) {
        this.idUsuario = idUsuario;
    }

    public Instant getFechaEvaluacion() {
        return fechaEvaluacion;
    }

    public void setFechaEvaluacion(Instant fechaEvaluacion) {
        this.fechaEvaluacion = fechaEvaluacion;
    }

    public List<RespuestaDTO> getRespuestas() {
        return respuestas;
    }

    public void setRespuestas(List<RespuestaDTO> respuestas) {
        this.respuestas = respuestas;
    }
}
