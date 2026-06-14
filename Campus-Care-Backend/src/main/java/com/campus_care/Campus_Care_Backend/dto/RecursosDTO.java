package com.campus_care.Campus_Care_Backend.dto;

import java.time.LocalDateTime;

public class RecursosDTO {

    private String id;
    private String titulo;
    private String contenido;
    private String urlEnlace;
    private String tipoRecursoId;
    private String tipoRecursoNombre;

    private String categoriaId;
    private String categoriaNombre;

    private LocalDateTime fechaPublicacion;
    private boolean activo;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getTitulo() {
        return titulo;
    }

    public void setTitulo(String titulo) {
        this.titulo = titulo;
    }

    public String getContenido() {
        return contenido;
    }

    public void setContenido(String contenido) {
        this.contenido = contenido;
    }

    public String getUrlEnlace() {
        return urlEnlace;
    }

    public void setUrlEnlace(String urlEnlace) {
        this.urlEnlace = urlEnlace;
    }

    public String getTipoRecursoId() {
        return tipoRecursoId;
    }

    public void setTipoRecursoId(String tipoRecursoId) {
        this.tipoRecursoId = tipoRecursoId;
    }

    public String getCategoriaId() {
        return categoriaId;
    }

    public void setCategoriaId(String categoriaId) {
        this.categoriaId = categoriaId;
    }

    public LocalDateTime getFechaPublicacion() {
        return fechaPublicacion;
    }

    public void setFechaPublicacion(LocalDateTime fechaPublicacion) {
        this.fechaPublicacion = fechaPublicacion;
    }

    public boolean isActivo() {
        return activo;
    }

    public void setActivo(boolean activo) {
        this.activo = activo;
    }
}
