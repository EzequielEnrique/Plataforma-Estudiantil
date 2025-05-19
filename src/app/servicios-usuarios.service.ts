import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class ServiciosUsuariosService {
  private apiUrl = 'http://localhost/Final/API/personas.php';
  private perData = {
    perId: "",
    perApellido: "",
    perNombre: "",
    perDni: "",
    perEmail: ""
  }

  constructor(private clienteHttp: HttpClient) { }

  EditarUsuario(usData: any): Observable<any> {
    return this.clienteHttp.put(this.apiUrl, usData);
  }

  
  getUsuarioData() {
    return this.perData;
  }

  setUsuarioData(perId: any ,perApellido: any, perNombre: any, perDni: any, perEmail?: any) {
    this.perData.perId = perId;
    this.perData.perApellido = perApellido;
    this.perData.perNombre = perNombre;
    this.perData.perDni = perDni;
    this.perData.perEmail = perEmail;
  }

  deleteUsuarioData(){
    this.perData.perId = "";
    this.perData.perApellido = "";
    this.perData.perNombre = "";
    this.perData.perDni = "";
    this.perData.perEmail = "";
  }

  cambiarContrasena(usuarioId: string, nuevaContrasena: string): Observable<any> {
    const body = {
      perId: usuarioId,
      perContrasena: nuevaContrasena,
      Authorization: localStorage.getItem('Token')
    };
    return this.clienteHttp.put(this.apiUrl, body);
  }
  
}

