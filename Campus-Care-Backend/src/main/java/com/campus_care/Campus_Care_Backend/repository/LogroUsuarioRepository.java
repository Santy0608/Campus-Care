package com.campus_care.Campus_Care_Backend.repository;

import com.campus_care.Campus_Care_Backend.domain.LogroUsuario;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.data.mongodb.repository.config.EnableMongoRepositories;

@EnableMongoRepositories
public interface LogroUsuarioRepository extends MongoRepository<LogroUsuario, String> {

    boolean existsByIdUsuarioAndIdLogro(String idUsuario, String idLogro);

}
