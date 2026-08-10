package com.campus_care.Campus_Care_Backend.domain;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;
import org.springframework.data.mongodb.core.mapping.Field;

import java.time.Instant;
import java.time.LocalDateTime;

@Document(collection = "logros_usuario")
public class LogroUsuario {

    @Id
    private String id;

    @Field("id_usuario")
    private String idUsuario;

    @Field("id_logro")
    private String idLogro;

    private Instant fechaDesbloqueo;

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

    public String getIdLogro() {
        return idLogro;
    }

    public void setIdLogro(String idLogro) {
        this.idLogro = idLogro;
    }

    public Instant getFechaDesbloqueo() {
        return fechaDesbloqueo;
    }

    public void setFechaDesbloqueo(Instant fechaDesbloqueo) {
        this.fechaDesbloqueo = fechaDesbloqueo;
    }
}