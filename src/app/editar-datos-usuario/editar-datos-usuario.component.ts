import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Router } from '@angular/router';
import { ServiciosUsuariosService } from '../servicios-usuarios.service';
import { Location } from '@angular/common'; 

@Component({
  selector: 'app-editar-datos-usuario',
  templateUrl: './editar-datos-usuario.component.html',
  styleUrls: ['./editar-datos-usuario.component.css']
})
export class EditarDatosUsuarioComponent {
  cargandoGuardado: boolean = false;

  constructor(
    private formB: FormBuilder,
    private _snackBar: MatSnackBar,
    private router: Router,
    private usService: ServiciosUsuariosService,
    private location: Location 

  ) { }

  formUsuario = this.formB.group({
    usApellido: ['', [Validators.required, Validators.minLength(3)]],
    usNombre: ['', [Validators.required, Validators.minLength(3)]],
    usCorreo: ['', [Validators.required, Validators.email]],
    usDni: ['', [Validators.required, Validators.minLength(7), Validators.maxLength(8)]]
  });


  ngOnInit(): void {
    if (this.usService.getUsuarioData().perDni == "") {
      this.router.navigate(['dashboard-bibliotecario']);
    }

    
    this.formUsuario.setValue({
      usApellido: this.usService.getUsuarioData().perApellido,
      usNombre: this.usService.getUsuarioData().perNombre,
      usCorreo: this.usService.getUsuarioData().perEmail,
      usDni: this.usService.getUsuarioData().perDni,
    })

  }


  btnCancelar(){
    this.usService.deleteUsuarioData();
    this.location.back(); 
  }

  onSubmit() {
    const token = localStorage.getItem('Token');
  
    const datos = {
      perApellido: this.formUsuario.value.usApellido,
      perNombre: this.formUsuario.value.usNombre,
      perDni: this.formUsuario.value.usDni,
      perMail: this.formUsuario.value.usCorreo,
      Authorization: token
    };
  
    if (this.formUsuario.valid) {
      this.cargandoGuardado = true;
      this.usService.EditarUsuario(datos).subscribe(
        (response) => {
          this.cargandoGuardado = false;
          this._snackBar.open('Datos actualizados correctamente', 'Cerrar', { duration: 5000 });
  
          // Resetear formulario tras guardar
          this.formUsuario.reset();
          this.formUsuario.setValue({
            usApellido: this.usService.getUsuarioData().perApellido,
            usNombre: this.usService.getUsuarioData().perNombre,
            usCorreo: this.usService.getUsuarioData().perEmail,
            usDni: this.usService.getUsuarioData().perDni,
          });
  
          this.usService.deleteUsuarioData();
          this.router.navigate(['dashboard-bibliotecario']);
        },
        (error) => {
          this.cargandoGuardado = false;
          this._snackBar.open('Error al actualizar los datos. Intenta nuevamente.', 'Cerrar', { duration: 5000 });
        }
      );
    } else {
      
      this.formUsuario.markAllAsTouched(); 
      this._snackBar.open('Por favor, corrige los errores antes de guardar.', 'Cerrar', { duration: 5000 });
    }
  }
}
