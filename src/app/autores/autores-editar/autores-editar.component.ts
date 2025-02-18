import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { FormGroup, FormBuilder } from '@angular/forms';
import { Autor, AutoresService } from 'src/app/autores.service';

@Component({
  selector: 'app-autores-editar',
  templateUrl: './autores-editar.component.html',
  styleUrls: ['./autores-editar.component.css']
})
export class AutoresEditarComponent implements OnInit {
  elID: any;
  formAutor: FormGroup;

  constructor(
    private formulario: FormBuilder,
    private rutaactiva: ActivatedRoute,
    private autoresService: AutoresService,
    private router: Router
  ) {
    this.formAutor = this.formulario.group({
      autNombre: [''],
      autApellido: [''],
      autFecNac: [''],
      autBio: [''],
      autFecDes: ['']
    });
  }

  ngOnInit(): void {
    this.elID = this.rutaactiva.snapshot.paramMap.get('id');
    console.log('ID del autor a editar:', this.elID); 
  
    this.autoresService.getAutorById(this.elID).subscribe((respuesta) => {
      console.log('Respuesta del servicio:', respuesta); 
      if (respuesta && respuesta.length > 0) { 
        const autor = respuesta[0]; 
        this.formAutor.patchValue({
          autNombre: autor.autNombre,
          autApellido: autor.autApellido,
          autFecNac: autor.autFecNac,
          autBio: autor.autBiografia, 
          autFecDes: autor.autFecDes
        });
        console.log('Formulario actualizado con los datos del autor:', this.formAutor.value); 
      }
    });
  }
  

  onSubmit(): void {
    const autorActualizado: Autor = {
      idAutor: this.elID,
      autNombre: this.formAutor.get('autNombre')?.value,
      autApellido: this.formAutor.get('autApellido')?.value,
      autFecNac: this.formAutor.get('autFecNac')?.value,
      autBiografia: this.formAutor.get('autBio')?.value,
      autFecDes: this.formAutor.get('autFecDes')?.value
    };

    console.log('Datos del autor a actualizar:', autorActualizado); 

    this.autoresService.editAutor(autorActualizado).subscribe({
      next: () => {
        console.log('Autor actualizado exitosamente'); 
        this.router.navigate(['autores-listar']);
      },
      error: (err) => {
        console.error('Error al editar el autor:', err); 
      }
    });
  }

  Cancelar(): void {
    console.log('Cancelando la edición del autor'); 
    this.router.navigateByUrl('autores-listar');
  }
}
