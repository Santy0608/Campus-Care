package com.campus_care.Campus_Care_Backend.dto;

public class CategoriaDTO {

    private String idCategoria;
    private String nombre;
    private String descripcion;

    public String getIdCategoria(){
        return idCategoria;
    }

    public void setIdCategoria(String idCategoria){
        this.idCategoria = idCategoria;
    }

    public String getNombre(){
        return nombre;
    }

    public void setNombre(String nombre){
        this.nombre = nombre;
    }


    public String getDescripcion(){
        return descripcion;
    }

    public void setDescripcion(String descripcion){
        this.descripcion = descripcion;
    }
}
