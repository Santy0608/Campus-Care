package com.campus_care.Campus_Care_Backend.dto;

import java.time.LocalDate;
import java.time.LocalDateTime;

public class DiarioDTO {

    private String id;
    private String idUsuario;
    private LocalDateTime fecha;
    private String entradaTexto;

    public String getId(){
        return id;
    }

    public void setId(String id){
        this.id = id;
    }

    public String getIdUsuario(){
        return idUsuario;
    }

    public void setIdUsuario(String idUsuario){
        this.idUsuario = idUsuario;
    }

    public LocalDateTime getFecha(){
        return fecha;
    }

    public void setFecha(LocalDateTime fecha){
        this.fecha = fecha;
    }

    public String getEntradaTexto(){
        return entradaTexto;
    }

    public void setEntradaTexto(String entradaTexto){
        this.entradaTexto = entradaTexto;
    }

}
