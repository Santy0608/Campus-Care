package com.campus_care.Campus_Care_Backend.dto;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.data.annotation.PersistenceCreator;

import java.time.Instant;
import java.time.LocalDate;
import java.time.LocalDateTime;

public class DiarioDTO {

    private String id;
    private String idUsuario;
    private Instant fecha;
    private String entradaTexto;

    // 1. Constructor vacío obligatorio para instanciación por reflexión/JavaBeans
    public DiarioDTO() {
    }

    // 2. Constructor mapeado explícitamente para Spring Data
    @PersistenceCreator
    public DiarioDTO(
            @Value("#root.id") String id,
            @Value("#root.idUsuario") String idUsuario,
            @Value("#root.fecha") Instant fecha,
            @Value("#root.entradaTexto") String entradaTexto) {
        this.id = id;
        this.idUsuario = idUsuario;
        this.fecha = fecha;
        this.entradaTexto = entradaTexto;
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
