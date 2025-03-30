import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

export interface Autor {
  idAutor?: number;
  autNombre: string;
  autApellido: string;
  autFecNac: string;
  autBiografia: string;
  autFecDes: string;
}

@Injectable({
  providedIn: 'root'
})
export class AutoresService {
  private apiUrl = 'http://localhost/Final/API/autores.php';

  constructor(private http: HttpClient) { }

  private getAuthParam(): string {
    return `Authorization=${localStorage.getItem('Token')}`;
  }

  getAutor(): Observable<Autor[]> {
    return this.http.get<Autor[]>(`${this.apiUrl}?${this.getAuthParam()}`);
  }

  getAutorById(id: number): Observable<Autor[]> {
    return this.http.get<Autor[]>(`${this.apiUrl}?idAutor=${id}&${this.getAuthParam()}`);
  }

  addAutor(autor: Autor): Observable<number> {
    return this.http.post<number>(`${this.apiUrl}?${this.getAuthParam()}`, autor);
  }

  editAutor(autor: Autor): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}?${this.getAuthParam()}`, autor);
  }

  delAutor(idAutor: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}?idAutor=${idAutor}&${this.getAuthParam()}`);
  }
}

