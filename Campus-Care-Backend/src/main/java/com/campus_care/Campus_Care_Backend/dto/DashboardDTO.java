package com.campus_care.Campus_Care_Backend.dto;

import java.util.List;

public class DashboardDTO {

    private List<String> fechas;

    private List<MetricaSerieDTO> series;

    public List<String> getFechas() {
        return fechas;
    }

    public void setFechas(List<String> fechas) {
        this.fechas = fechas;
    }

    public List<MetricaSerieDTO> getSeries() {
        return series;
    }

    public void setSeries(List<MetricaSerieDTO> series) {
        this.series = series;
    }
}
