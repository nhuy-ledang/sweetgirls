import { Component, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { EmailValidator } from '../../@theme/validators';
import { Security } from '../../@core/security';
import { Usr } from '../../@core/entities';
import { GlobalState } from '../../@core/utils';

@Component({
  selector: 'ngx-login',
  templateUrl: './login.component.html',
})

export class LoginComponent implements OnDestroy {
  showValid: boolean = false;
  form: FormGroup;
  email: AbstractControl;
  password: AbstractControl;
  remember: AbstractControl;
  passType: 'password' | 'text' = 'password';

  constructor(fb: FormBuilder, protected _router: Router, protected _security: Security, protected _state: GlobalState) {
    this.form = fb.group({
      'email': ['', Validators.compose([Validators.required, EmailValidator.validate])],
      'password': ['', Validators.compose([Validators.required])],
      'remember': [false],
    });

    this.email = this.form.controls['email'];
    this.password = this.form.controls['password'];
    this.remember = this.form.controls['remember'];
  }

  ngOnDestroy(): void {
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      this._security.login(params).then((user: Usr) => {
          if (user.isAccessAdmin()) {
            this._router.navigate(['/']);
          } else {
            console.log(user);
          }
        }, (res: any) => {
          console.log(res);
          this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
        },
      );
    }
  }

  changePassType(): void {
    this.passType = this.passType === 'password' ? 'text' : 'password';
  }
}
