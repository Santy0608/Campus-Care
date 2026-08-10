package com.campus_care.Campus_Care_Backend.dto;

public class CatalogoLogrosDTO {

    private String id;
    private String titulo;
    private String descripcion;
    private Integer puntosOtorgados;
    private String criterioRacha;
    private String urlIcono;

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

    public String getDescripcion() {
        return descripcion;
    }

    public void setDescripcion(String descripcion) {
        this.descripcion = descripcion;
    }

    public Integer getPuntosOtorgados() {
        return puntosOtorgados;
    }

    public void setPuntosOtorgados(Integer puntosOtorgados) {
        this.puntosOtorgados = puntosOtorgados;
    }

    public String getCriterioRacha() {
        return criterioRacha;
    }

    public void setCriterioRacha(String criterioRacha) {
        this.criterioRacha = criterioRacha;
    }

    public String getUrlIcono() {
        return urlIcono;
    }

    public void setUrlIcono(String urlIcono) {
        this.urlIcono = urlIcono;
    }
}
