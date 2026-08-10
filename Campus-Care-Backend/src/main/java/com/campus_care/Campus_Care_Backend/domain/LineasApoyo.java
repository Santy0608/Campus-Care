package com.campus_care.Campus_Care_Backend.domain;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;
import org.springframework.data.mongodb.core.mapping.Field;

@Document(collection = "lineas_apoyo")
public class LineasApoyo {

    @Id
    private String id;
    @Field("nombre_institucion")
    private String nombreInstitucion;
    @Field("telefono")
    private String telefono;
    @Field("horario_atencion")
    private String horarioAtencion;
    @Field("url_sitio")
    private String urlSitio;
    @Field("activo")
    private boolean activo;


    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getNombreInstitucion() {
        return nombreInstitucion;
    }

    public void setNombreInstitucion(String nombreInstitucion) {
        this.nombreInstitucion = nombreInstitucion;
    }

    public String getTelefono() {
        return telefono;
    }

    public void setTelefono(String telefono) {
        this.telefono = telefono;
    }

    public String getHorarioAtencion() {
        return horarioAtencion;
    }

    public void setHorarioAtencion(String horarioAtencion) {
        this.horarioAtencion = horarioAtencion;
    }

    public String getUrlSitio() {
        return urlSitio;
    }

    public void setUrlSitio(String urlSitio) {
        this.urlSitio = urlSitio;
    }

    public boolean isActivo() {
        return activo;
    }

    public void setActivo(boolean activo) {
        this.activo = activo;
    }
}

