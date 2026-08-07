package com.campus_care.Campus_Care_Backend.domain;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;
import org.springframework.data.mongodb.core.mapping.Field;

import java.time.Instant;

@Document(collection = "historial_puntos")
public class HistorialPuntos {

    @Id
    String id;
    @Field("id_usuario")
    private String idUsuario;
    @Field("puntos_ganados")
    private Integer puntosGanados;
    private String concepto;
    private Instant fecha;

    public String getId(){
        return id;
    }

    public String getIdUsuario(){
        return idUsuario;
    }

    public void setIdUsuario(String idUsuario){
        this.idUsuario = idUsuario;
    }

    public Integer getPuntosGanados(){
        return puntosGanados;
    }

    public void setPuntosGanados(Integer puntosGanados){
        this.puntosGanados = puntosGanados;
    }

    public String getConcepto(){
        return concepto;
    }

    public void setConcepto(String concepto){
        this.concepto = concepto;
    }

    public Instant getFecha(){
        return fecha;
    }

    public void setFecha(Instant fecha){
        this.fecha = fecha;
    }


}
