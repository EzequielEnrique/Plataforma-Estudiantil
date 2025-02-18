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
    
    this.autorservice.addAutor(this.autor).subscribe(
      (data) => {
        console.log('Autor creado con éxito', data);
        
        this.router.navigateByUrl('autores-listar');
      },
      (error) => {
        console.error('Error al crear el autor', error);
      }
    );
  }

  Cancelar(): void {
    console.log('Cancelando la edición del autor'); 
    this.router.navigateByUrl('autores-listar');
  }
}