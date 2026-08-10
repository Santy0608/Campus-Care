package com.campus_care.Campus_Care_Backend.dto;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.cglib.core.Local;
import org.springframework.data.annotation.PersistenceCreator;

import java.time.Instant;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.Date;

public class DiarioDTO {

    private String id;
    private String idUsuario;
    private Instant fecha;
    private String entradaTexto;

    // 1. Constructor vacío obligatorio para instanciación por reflexión/JavaBeans
    public DiarioDTO(){

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

    public Instant getFecha() {
        return fecha;
    }

    public void setFecha(Instant fecha) {
        this.fecha = fecha;
    }

    public String getEntradaTexto() {
        return entradaTexto;
    }

    public void setEntradaTexto(String entradaTexto) {
        this.entradaTexto = entradaTexto;
    }
}
