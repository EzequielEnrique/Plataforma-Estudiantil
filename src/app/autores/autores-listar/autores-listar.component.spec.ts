import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AutoresListarComponent } from './autores-listar.component';

describe('AutoresListarComponent', () => {
  let component: AutoresListarComponent;
  let fixture: ComponentFixture<AutoresListarComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AutoresListarComponent]
    });
    fixture = TestBed.createComponent(AutoresListarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
