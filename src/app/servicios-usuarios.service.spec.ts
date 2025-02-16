import { TestBed } from '@angular/core/testing';

import { ServiciosUsuariosService } from './servicios-usuarios.service';

describe('ServiciosUsuariosService', () => {
  let service: ServiciosUsuariosService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(ServiciosUsuariosService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
