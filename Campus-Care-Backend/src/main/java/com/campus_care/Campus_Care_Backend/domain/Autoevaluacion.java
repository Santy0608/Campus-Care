package com.campus_care.Campus_Care_Backend.domain;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;
import org.springframework.data.mongodb.core.mapping.Field;

import java.time.Instant;
import java.time.LocalDateTime;
import java.util.List;

@Document(collection = "autoevaluaciones")
public class Autoevaluacion {

    @Id
    String id;
    @Field("id_usuario")
    private String idUsuario;
    private Instant fechaEvaluacion;
    private List<Respuesta> respuestas;

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

    public List<Respuesta> getRespuestas() {
        return respuestas;
    }

    public void setRespuestas(List<Respuesta> respuestas) {
        this.respuestas = respuestas;
    }
}

