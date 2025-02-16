import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Autor, AutoresService } from 'src/app/autores.service';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-autores-cargar',
  templateUrl: './autores-cargar.component.html',
  styleUrls: ['./autores-cargar.component.css']
})
export class AutoresCargarComponent implements OnInit {
  
  // El modelo de autor que será llenado desde el formulario
  autor: Autor = {
    autNombre: '',
    autApellido: '',
    autFecNac: '',
    autBiografia: '',
    autFecDes: ''
  };

  constructor(
    private autorservice: AutoresService,
    private router: Router
  ) {}

  ngOnInit(): void {}

  onSubmit(): void {
    // Llamamos al servicio para agregar un nuevo autor
    this.autorservice.addAutor(this.autor).subscribe(
      (data) => {
        console.log('Autor creado con éxito', data);
        // Después de crear el autor, puedes redirigir a otra página, por ejemplo, la lista de autores
        this.router.navigateByUrl('autores-listar');
      },
      (error) => {
        console.error('Error al crear el autor', error);
      }
    );
  }

  Cancelar(): void {
    console.log('Cancelando la edición del autor'); // Confirma que se está cancelando
    this.router.navigateByUrl('autores-listar');
  }
}