<template>
  <div class="ks-column ks-page">
    <!-- Modal -->
    <div class="modal fade" id="editCriticalStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">EDITAR</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
            <div class="form-group">
              <label for="exampleInputEmail1">CODIGO</label>
              <input type="text" disabled v-model="editCode" class="form-control">
              <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">DIAS</label>
              <input type="number" v-model="editDays" class="form-control">
            </div>
          </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
            <button type="button" @click="formEditSave()" class="btn btn-default">GUARDAR</button>
          </div>
        </div>
      </div>
    </div>
    <div class="ks-page-header">
        <section class="ks-title-and-subtitle mt-2">
            <div class="ks-title-block">
                <h3 class="ks-main-title">Perfil / Configuración</h3>
                <div class="ks-sub-title">Configuracion de pagina y perfil.</div>
            </div>
        </section>
    </div>
    <div class="ks-page-content">
        <div class="ks-page-content-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-3">
                        <div class="card ks-crm-contact-user-column">
                            <div class="ks-crm-contact-user-column-main-info">
                                <section>
                                    <img :src="this.photo ? this.photo : '/img/user_default.jpeg'" width="100" height="100" class="ks-crm-contact-user-avatar rounded-circle">
                                </section>
                                <section>
                                    <div class="ks-crm-contact-user-name">{{ this.name }} {{ this.last_name }}</div>
                                    <div class="ks-crm-contact-user-location">{{ this.city }}</div>
                                    <div class="ks-crm-contact-user-rating">
                                        <i class="la la-star ks-star" aria-hidden="true"></i>
                                        <i class="la la-star ks-star" aria-hidden="true"></i>
                                        <i class="la la-star ks-star" aria-hidden="true"></i>
                                        <i class="la la-star ks-star" aria-hidden="true"></i>
                                        <i class="la la-star ks-star" aria-hidden="true"></i>
                                        <!--<i class="la la-star-half-o ks-star" aria-hidden="true"></i>-->
                                        <hr>
                                        <div class="badge badge-success" v-if="usertype == 1">
                                            ADMINISTRADOR
                                        </div>
                                        <div class="badge badge-success" v-if="usertype != 1">
                                            VENDEDOR
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div class="ks-crm-contact-user-column-custom-info">
                                <h6 class="ks-custom-info-header">Información de contacto</h6>
                                <div class="ks-custom-info-item">
                                    <div class="ks-custom-info-item-name">Email</div>
                                    <div class="ks-custom-info-item-content">{{ this.email }}</div>
                                </div>
                                <div class="ks-custom-info-item">
                                    <div class="ks-custom-info-item-name">Telefono</div>
                                    <div class="ks-custom-info-item-content">{{ this.phone }}</div>
                                </div>
                                <div class="ks-custom-info-item">
                                    <div class="ks-custom-info-item-name">Dirección</div>
                                    <div class="ks-custom-info-item-content">{{ this.address }}</div>
                                </div>
                            </div>
                            <div class="ks-crm-contact-user-column-custom-info">
                                <h6 class="ks-custom-info-header">Custom Information</h6>
                                <div class="ks-custom-info-item">
                                    <div class="ks-custom-info-item-name">Facebook</div>
                                    <div class="ks-custom-info-item-content"><a href="#">{{ this.facebook_link }}</a></div>
                                </div>
                                <div class="ks-custom-info-item">
                                    <div class="ks-custom-info-item-name">Numero Empresa</div>
                                    <div class="ks-custom-info-item-content">{{ this.company_number }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        <div class="ks-crm-contact-user-tabs-column">
                            <div class="ks-tabs-container ks-tabs-default ks-tabs-no-separator">
                                <ul class="nav ks-nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#" data-toggle="tab" data-target="#crm-tab-tasks" aria-expanded="false">PERFIL DE USUARIO</a>
                                    </li>
                                    <li class="nav-item" v-if="usertype == 1">
                                        <a class="nav-link" href="#" data-toggle="tab" data-target="#crm-tab-events" aria-expanded="false">CONFIG. GENERAL</a>
                                    </li>
                                    <li class="nav-item" v-if="usertype == 1 && company_billing != 0">
                                        <a class="nav-link" href="#" data-toggle="tab" data-target="#crm-tab-activity" aria-expanded="true">CONFIG. BSALE</a>
                                    </li>
                                    <!--<li class="nav-item" v-if="usertype == 1">
                                        <a class="nav-link" href="#" data-toggle="tab" data-target="#crm-tab-notes" aria-expanded="false">CONFIG. LIBREDTE</a>
                                    </li>-->
                                    <li class="nav-item" v-if="usertype == 1">
                                        <a class="nav-link" href="#" data-toggle="tab" data-target="#crm-tab-stock" aria-expanded="false">STOCK CRITICO</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                  <div class="tab-pane  active" id="crm-tab-tasks" role="tabpanel" aria-expanded="false" data-height="735">
                                    <form>
                                    <div class="row">
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">NOMBRE(s)</label>
                                          <input type="text" v-model="name" class="form-control" aria-describedby="emailHelp">
                                          <small id="emailHelp" class="form-text text-muted">Ingrese su(s) nombre(s).</small>
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="exampleInputPassword1">APELLIDOS</label>
                                          <input type="text" v-model="last_name" class="form-control">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">CONTRASEÑA</label>
                                          <input type="password" v-model="password" class="form-control">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">EMAIL</label>
                                          <input type="email" v-model="email" class="form-control">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">TELEFONO</label>
                                          <input type="text" v-model="phone" class="form-control">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">CIUDAD</label>
                                          <input type="text" v-model="city" class="form-control">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">DIRECCIÓN</label>
                                          <input type="text" v-model="address" class="form-control">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">FACEBOOK</label>
                                          <input type="text" v-model="facebook_link" class="form-control">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">NUMERO DE EMPRESA</label>
                                          <input type="text" v-model="company_number" class="form-control">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">AVATAR (imagen 500kb max)</label>
                                          <input type="file" accept="image/*" id="uploadFile" v-on:change="uploadPhoto" class="form-control-file">
                                        </div>
                                      </div>
                                    </div>
                                    <button type="button" @click="formUserSave()" class="btn btn-default">GUARDAR</button>
                                    </form>
                                  </div>
                                    <div v-if="usertype == 1" class="tab-pane" id="crm-tab-events" role="tabpanel" aria-expanded="false" data-height="735">
                                      <p v-if="this.form_general_errors.length">
                                         <b>Por favor, corrija el(los) siguiente(s) error(es):</b>
                                         <div v-if="this.form_general_errors.length" class="alert alert-danger" role="alert">
                                           <ul>
                                             <li v-for="error in form_general_errors">{{ error }}</li>
                                           </ul>
                                        </div>
                                       </p>
                                       Visita tu tienda online en el siguiente link <a :href="'/mall/'+this.shop_url" target="_blank">Mi tienda</a>
                                       <hr>
                                      <form>
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="form-group">
                                              <label for="exampleInputEmail1">NOMBRE DE LA TIENDA</label>
                                              <input type="text" v-model="name_ecommerce" class="form-control">
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">EMPRESA DE FACTURACION</label>
                                              <select class="form-control" v-model="company_billing">
                                                <option value="0">NINGUNA</option>
                                                <option value="1">BSALE</option>
                                              </select>
                                            </div>
                                            <div class="form-group" v-if="company_billing == 0">
                                              <label for="exampleInputPassword1">LOGO EN EL VOUCHER</label>
                                              <select class="form-control" v-model="voucher_logo">
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                              </select>
                                            </div>
                                            <div class="form-group" v-if="company_billing == 0">
                                              <label for="exampleInputPassword1">FORMATO POR DEFECTO (VOUCHER)</label>
                                              <select class="form-control" v-model="continuous_paper_type">
                                                <option value="58">PAPEL CONTINUO 58MM</option>
                                                <option value="80">PAPEL CONTINUO 80MM</option>                                                
                                              </select>
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">HOJA DE ESTILO</label>
                                              <input type="text" class="form-control" v-model="css_config">
                                            </div> 
                                            <div class="form-group" v-if="company_billing != 0">
                                              <label for="exampleInputPassword1">ENTORNO DE FACTURACION</label>
                                              <select class="form-control" v-model="type_of_environment">
                                                <option value="1">PRODUCCION</option>
                                                <option value="2">CERTIFICACION (PRUEBAS)</option>
                                              </select>
<!--                                               <small class="form-text text-muted">Corresponde al token de produccion y certificacion.</small>
 -->                                            </div>
                                            <div class="form-group" v-if="company_billing != 0">
                                              <label for="exampleInputPassword1">FORMATO POR DEFECTO (BOLETAS)</label>
                                              <select class="form-control" v-model="ticket_default_format">
                                                <option value="1">HOJA CARTA</option>
                                                <option value="2">PAPEL CONTINUO</option>
                                              </select>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="form-group" v-if="company_billing != 0">
                                              <label for="exampleInputPassword1">FORMATO POR DEFECTO (FACTURAS)</label>
                                              <select class="form-control" v-model="invoice_default_format">
                                                <option value="1">HOJA CARTA</option>
                                                <option value="2">PAPEL CONTINUO</option>
                                              </select>
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">DOCUMENTO POR DEFECTO EN LA TIENDA</label>
                                              <select class="form-control" v-model="default_document_type">
                                                <!-- <option value="0">SOLO PROCESAR</option> -->
                                                <option value="1">BOLETA</option>
                                                <option value="2">FACTURA</option>
                                                <option value="3">COTIZACION</option>
                                              </select>
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">METODO DE PAGO POR DEFECTO</label>
                                              <select class="form-control" v-model="default_payment_method">
                                                <option value="1" selected="">EFECTIVO</option>
                                                <option value="2">CREDITO</option>
                                                <option value="6">DEBITO</option>
                                                <option value="8">TRANSFERENCIA</option>
                                              </select>
<!--                                               <small class="form-text text-muted">Corresponde al metodo por defecto al generar un documento.</small>
 -->                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">IMPRESORA</label>
                                              <select class="form-control" v-model="printer">
                                                <option value="0">LOCAL</option>
                                                <option value="1">REMOTA</option>
                                              </select>
                                            </div>
                                            <div class="form-group" v-if="this.printer == 0">
                                              <label for="exampleInputPassword1">NOMBRE DE LA IMPRESORA</label>
                                              <input type="text" v-model="printer_name" class="form-control">
                                            </div>
                                            <div class="form-group" v-if="this.printer == 0">
                                              <label for="exampleInputPassword1">IMPRIMIR VOUCHER</label>
                                              <select class="form-control" v-model="optional_printer">
                                                <option value="0">NO</option>
                                                <option value="1">SI</option>
                                              </select>
                                            </div>                                                                           
                                          </div>
                                        </div>
                                      <button type="button" @click="GeneralConfigSave()" class="btn btn-default">GUARDAR</button>
                                      </form>
                                    </div>
                                    <div v-if="usertype == 1" class="tab-pane ks-scrollable" id="crm-tab-activity" role="tabpanel" aria-expanded="true" data-height="735">
                                      <form>
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="form-group">
                                              <label for="exampleInputEmail1">URL (GENERACION DE DOCUMENTOS)</label>
                                              <input type="email" v-model="url_documents" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                                              <small class="form-text text-muted">Puede encontrarlo en el siguiente link <a href="https://github.com/gmontero/ex-documentacion/wiki/Documentos#post-un-documento" target="_blank">Bsale</a> </small>
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">ID DOCUMENTO FACTURA (Formato Carta)</label>
                                              <input type="number" v-model="invoice_id_letter" class="form-control">
                                              <small class="form-text text-muted">Puede encontrarlo en el siguiente link <a href="https://github.com/gmontero/ex-documentacion/wiki/Tipos-de-documento#get-lista-de-tipos-de-documento" target="_blank">Bsale</a> </small>
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">ID DOCUMENTO FACTURA (Formato termico)</label>
                                              <input type="number" v-model="invoice_id_thermal" class="form-control">
                                              <small class="form-text text-muted">Puede encontrarlo en el siguiente link <a href="https://github.com/gmontero/ex-documentacion/wiki/Tipos-de-documento#get-lista-de-tipos-de-documento" target="_blank">Bsale</a> </small>
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">ID DOCUMENTO BOLETA (Formato Carta)</label>
                                              <input type="number" v-model="ticket_id_letter" class="form-control">
                                              <small class="form-text text-muted">Puede encontrarlo en el siguiente link <a href="https://github.com/gmontero/ex-documentacion/wiki/Tipos-de-documento#get-lista-de-tipos-de-documento" target="_blank">Bsale</a> </small>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">ID DOCUMENTO BOLETA (Formato termico)</label>
                                              <input type="number" v-model="ticket_id_thermal" class="form-control">
                                              <small class="form-text text-muted">Puede encontrarlo en el siguiente link <a href="https://github.com/gmontero/ex-documentacion/wiki/Tipos-de-documento#get-lista-de-tipos-de-documento" target="_blank">Bsale</a> </small>
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">ID DOCUMENTO COTIZACION (Formato carta)</label>
                                              <input type="number" v-model="quotation_id_letter" class="form-control">
                                              <small class="form-text text-muted">Puede encontrarlo en el siguiente link <a href="https://github.com/gmontero/ex-documentacion/wiki/Tipos-de-documento#get-lista-de-tipos-de-documento" target="_blank">Bsale</a> </small>
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">TOKEN DE PRODUCCION</label>
                                              <input type="password" v-model="token_production" class="form-control">
                                              <small class="form-text text-muted">Necesario para generar documentos autenticados por el Sii. </small>
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputPassword1">TOKEN DE CERTIFICACION O PRUEBAS</label>
                                              <input type="password" v-model="token_certification" class="form-control">
                                              <small class="form-text text-muted">Necesario para generar documentos de prueba. </small>
                                            </div>
                                          </div>
                                        </div>
                                        <button type="button" @click="BsaleConfigSave()" class="btn btn-default">GUARDAR</button>
                                      </form>
                                    </div>
                                    <div v-if="usertype == 1" class="tab-pane" id="crm-tab-notes" role="tabpanel" aria-expanded="false" data-height="735">
                                        Proximamente...
                                    </div>
                                    <div v-if="usertype == 1" class="tab-pane" id="crm-tab-stock" role="tabpanel" aria-expanded="false" data-height="735">
                                      <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                          <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">CONFIGURACION</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                          <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">AGREGAR ITEMS</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                          <a class="nav-link" id="profile-tab" data-toggle="tab" href="#critic" role="tab" aria-controls="critic" aria-selected="false">ITEMS CRITICOS</a>
                                        </li>
                                      </ul>
                                      <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                          <form>
                                            <small style="font-size:13px;" class="form-text text-muted">Un Stock Crítico lo definimos como aquellos productos que tienen mayor rodaje en sus ventas y por el cual usted debe saber con anterioridad cuantas unidades tiene para no quedar en 0.<br><br>
                                              Es por esto que existe esta funcionalidad, el cual le permite configurar aquellos productos importantes para nunca quedar en 0. El sistema le avisará a través de un correo cuando estén pronto a agotarse.
                                            </small>
                                              <hr>
                                            <div class="form-group">
                                              <label for="exampleInputEmail1">¿QUIERES RECIBIR ALERTAS POR STOCK CRITICO?</label>
                                              <select class="form-control" v-model="notification_status" name="">
                                                <option value="0" selected>No</option>
                                                <option value="1">Si</option>
                                              </select>
                                              <small id="emailHelp" class="form-text text-muted">Activa o desactiva las notificaciones.</small>
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputEmail1">¿A QUE CORREO QUIERES QUE TE ENVIEMOS NOTIFICACIONES?</label>
                                              <input type="email" v-model="notification_email" class="form-control">
                                            </div>
                                            <div class="form-group">
                                              <label for="exampleInputEmail1">¿EL INDICE DE REPOSICION LO OBTENEMOS EN BASE A LOS ULTIMOS 4?</label>
                                              <select class="form-control" v-model="notification_range">
                                                <option value="0">Seleccionar</option>
                                                <option value="dias">Dias</option>
                                                <option value="semanas">Semanas</option>
                                                <option value="meses">Meses</option>
                                              </select>
                                            </div>
                                            <!--<div class="form-group">
                                              <label for="exampleInputEmail1">¿NOTIFICAR CUANDO ME QUEDEN MENOS DE?</label>
                                              <input type="number"  class="form-control"> DIAS DE STOCK
                                            </div>-->
                                            <button type="button" @click="formStockSave()" class="btn btn-default">GUARDAR</button>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                          <button title="QUITAR COLUMNA" @click="inputSet(1)" class="btn btn-default btn-sm" type="button" name="button"><font-awesome-icon :icon="['fas', 'minus']" /></button>
                                          <button title="AGREGAR COLUMNA" @click="inputSet(2)" class="btn btn-default btn-sm" type="button" name="button"><font-awesome-icon :icon="['fas', 'plus']" /></button>
                                          <button type="button" @click="formCriticalSave()" class="btn btn-default btn-sm">GUARDAR</button>
                                          <div v-if="errors.length" class="alert alert-danger mt-3" role="alert">
                                          <ul>
                                             <li v-for="error in errors">{{ error }}</li>
                                           </ul>
                                          </div>
                                          <table class="table table-sm table-hover mt-4">
                                            <tr v-for="c,index in i" :key="index" :id="index">
                                              <td width="10%" class="img" :id="'i_'+index">
                                              </td>
                                              <td width="45%">
                                                <input v-model="code[index]" v-on:change="checkCode(index, $event.target.value)" class="form-control" v-on:keydown.enter.prevent type="text"  placeholder="ESCANEAR O INGRESAR MANUAL">
                                              </td>
                                              <td width="45%">
                                                <input v-model="days[index]" class="form-control" v-on:keydown.enter.prevent type="text" placeholder="DIAS">
                                              </td>
                                              <td width="5%">
                                                <button @click="RemoveRow(index)" class="btn btn-default btn-sm mt-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td colspan="4"></td>
                                            </tr>
                                          </table>
                                      </div>
                                      <div v-if="usertype == 1" class="tab-pane fade" id="critic" role="tabpanel" aria-labelledby="critic-tab">
                                        <table class="table table-sm table-hover">
                                          <thead>
                                            <tr>
                                              <th>CODIGO</th>
                                              <th>NOTIFICAR CUANDO ME QUEDEN </th>
                                              <th>ACCIÓN</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <tr v-for="c in this.criticItems.data" :id="'tr_'+c.code">
                                              <td>{{ c.code }}</td>
                                              <td :id="'days_'+c.code">{{ c.days }} dias de stock</td>
                                              <td>
                                                <button title="EDITAR" @click="editCritical(c)"  class="mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'edit']" /></button>
                                                <button title="ELIMINAR" @click="deleteCritical(c)"  type="button" name="button"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                        <hr>
                                        <pagination :limit="6" :data="criticItems" @pagination-change-page="CriticItemsCharge">
                                        </pagination>
                                      </div>
                                   </div>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          </div>
      </div>
  </div>
</template>

<script>
import Vue from "vue";
import VueSweetalert2 from "vue-sweetalert2";
import $ from 'jquery'

Vue.use(VueSweetalert2);

export default {
  data() {
    return {
      shop_url: '',
      // ITEM CRITICO
      criticItems: {},
      criticTable: 0,
      codeError: false,
      errors: [],
      code: [],
      days: [],
      i: 1,
      // CRITICAL STOCK
      notification_status: 0,
      notification_email: "",
      notification_range: 0,
      // EDIT FORM
      editCode: 0,
      editDays: 0,
      // CONFIG DE USUARIO
      name: "",
      last_name: "",
      password: "",
      email: "",
      phone: "",
      city: "",
      address: "",
      facebook_link: "",
      company_number: "",
      photo: "",
      size: false,

      // CONFIG GENERAL
      form_general_errors: [],
      name_ecommerce: "",
      company_billing: 1,
      voucher_logo: 0,
      continuous_paper_type: 58,
      type_of_environment: 2,
      ticket_default_format: 2,
      invoice_default_format: 2,
      default_document_type: 1,
      default_payment_method: 1,
      printer: 0,
      optional_printer: 0,
      printer_name: "",
      css_config: "",

      // API CONFIG BSALE
      form_api_bsale_errors: [],
      url_documents: "",
      invoice_id_letter: "",
      invoice_id_thermal: "",
      ticket_id_letter: "",
      ticket_id_thermal: "",
      quotation_id_letter: "",
      token_production: "",
      token_certification: "",
    };
  },
  mounted() {
    this.UserProfileCharge();
    this.GeneralConfigCharge();
    if (this.usertype == 1) {
      this.BsaleConfigCharge();
      this.ConfigStockCharge();
      this.CriticItemsCharge();
    }
  },
  computed: {
    usertype() {
      return this.$store.getters.getUserType;
    },
  },
  methods: {
    deleteCritical(c) {
      this.$swal
        .fire({
          title: "Esta seguro?",
          text: "No podra revertir esto!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#28a745",
          cancelButtonColor: "#d33",
          confirmButtonText: "Si, eliminar!",
        })
        .then((result) => {
          if (result.value) {
            axios.delete("/critical_items/" + c.code).then((response) => {
              this.CriticItemsCharge();
              this.$swal.fire(
                "Eliminado!",
                "La configuracion del codigo <b>" +
                  c.code +
                  "</b> ha sido eliminada.",
                "success"
              );
            });
          }
        });
    },
    formEditSave() {
      let formData = new FormData();
      formData.append("code", this.editCode);
      formData.append("days", this.editDays);
      axios.post("/critical_items/update", formData).then((response) => {
        $("#days_" + this.editCode).html(this.editDays);
        $("#tr_" + this.editCode).addClass("table-success");
        $("#editCriticalStock").modal("hide");
        this.$swal.fire({
          position: "center",
          icon: "success",
          title: "Codigo editado",
          html:
            "El codigo <b>" +
            this.editCode +
            "</b> ha sido editado correctamente.",
          showConfirmButton: true,
          //timer: 1500
        });
      });
    },
    editCritical(c) {
      jQuery.noConflict();
      this.editCode = c.code;
      this.editDays = c.days;
      $("#editCriticalStock").modal("show");
    },
    CriticItemsCharge(page = 1) {
      axios.post("/critical_items_get?page=" + page).then((response) => {
        this.criticItems = response.data;
      });
    },
    formCriticalSave() {
      this.errors = [];
      if (
        this.code.length != this.days.length ||
        this.code.length == 0 ||
        this.days.length == 0
      ) {
        this.errors.push("El campo codigo y/o dias no puede estar vacio.");
        return;
      }
      if (this.codeError == true) {
        this.errors.push("Por favor ingrese un codigo valido.");
        return;
      }
      let formData = new FormData();
      for (var i = 0; i < this.code.length; i++) {
        let code = this.code[i];
        formData.append("code[" + i + "]", code);
      }
      for (var i = 0; i < this.days.length; i++) {
        let days = this.days[i];
        formData.append("days[" + i + "]", days);
      }
      axios.post("/critical_items", formData).then((response) => {
        //console.log(response);
        if (response.data.items) {
          this.code = [];
          this.days = [];
          this.CriticItemsCharge();
          this.$swal.fire({
            position: "center",
            icon: "warning",
            title: "Configuracion guardada.",
            html:
              "Sin embargo los siguientes items " +
              "no se guardaron ya que existen en configuración. <br>" +
              response.data.items,
            showConfirmButton: true,
          });
        } else {
          this.code = [];
          this.days = [];
          this.CriticItemsCharge();
          this.MessageOK();
        }
      });
    },
    RemoveRow(index) {
      $("#" + index).remove();
    },
    inputSet(operator) {
      if (operator == 1) {
        if (this.i == 1) {
          return;
        } else {
          --this.i;
        }
      } else {
        ++this.i;
      }
    },
    checkCode(index, code) {
      axios.post("/check/code/" + code).then((response) => {
        if (response.data.item == 0) {
          $("#i_" + index).html(
            '<img title="EL CODIGO INGRESADO NO EXISTE EN ITEMS, ASEGURESE DE QUE EXISTA ANTES DE AGREGARLO." width="35" src="https://nonperfect.files.wordpress.com/2009/11/atencion.png" alt="">'
          );
          this.codeError = true;
        } else {
          $("#i_" + index).html(
            '<img title="EL CODIGO INGRESADO ES CORRECTO" width="35" src="https://findicons.com/files/icons/2117/nuove/128/camera_test.png" alt="">'
          );
          this.codeError = false;
        }
      });
    },
    uploadPhoto(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      if (file["size"] < 524000) {
        reader.onloadend = (file) => {
          //console.log('RESULT', reader.result)
          this.photo = reader.result;
          this.size = false;
        };
        reader.readAsDataURL(file);
      } else {
        this.size = true;
        this.$swal.fire({
          title: "Error",
          icon: "error",
          html:
            "El archivo <strong>" +
            file.name +
            "</strong> supero el limite de 500kb.",
          showCloseButton: false,
          focusConfirm: true,
          confirmButtonText: "OK",
        });
        this.$Progress.fail();
        return;
      }
    },
    formStockSave() {
      let formData = new FormData();
      formData.append("notification_status", this.notification_status);
      formData.append("notification_email", this.notification_email);
      formData.append("notification_range", this.notification_range);
      axios
        .post("/general_config_critical_stock", formData)
        .then((response) => {
          //console.log(response);
          this.MessageOK();
        });
    },
    formUserSave() {
      if (this.size) {
        this.$swal.fire({
          title: "Error",
          icon: "error",
          html: "El archivo supero el limite de 500kb, por favor intente con otra imagen.",
          showCloseButton: false,
          focusConfirm: true,
          confirmButtonText: "OK",
        });
        this.$Progress.fail();
        return;
      }
      let formData = new FormData();
      let image = document.getElementById("uploadFile").value;
      formData.append("name", this.name);
      formData.append("last_name", this.last_name);
      formData.append("password", this.password);
      formData.append("email", this.email);
      formData.append("phone", this.phone);
      formData.append("city", this.city);
      formData.append("address", this.address);
      formData.append("facebook_link", this.facebook_link);
      formData.append("company_number", this.company_number);
      if (image) {
        formData.append("photo", this.photo);
      }
      axios
        .post("/profile_config_save", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          //console.log(response);
          this.$store.commit("setphoto", this.photo);
          this.$store.commit("setname", this.name);
          document.getElementById("uploadFile").value = [];
          this.MessageOK();
        })
        .catch((error) => {
          // Error 😨
          if (error.response) {
            /*
             * The request was made and the server responded with a
             * status code that falls out of the range of 2xx
             */
            console.log(error.response.data);
            console.log(error.response.status);
            console.log(error.response.headers);
          } else if (error.request) {
            /*
             * The request was made but no response was received, `error.request`
             * is an instance of XMLHttpRequest in the browser and an instance
             * of http.ClientRequest in Node.js
             */
            console.log(error.request);
          } else {
            // Something happened in setting up the request and triggered an Error
            console.log("Error", error.message);
          }
          console.log(error.config);
        });
    },
    MessageOK() {
      this.$swal.fire({
        position: "center",
        icon: "success",
        title: "La configuración se ha guardado exitosamente.",
        showConfirmButton: false,
        timer: 1500,
      });
    },
    ConfigStockCharge() {
      axios.post("/general_config_critical_get").then((response) => {
        this.notification_status = response.data.notification_status;
        this.notification_email = response.data.notification_email;
        this.notification_range = response.data.notification_range;
      });
    },
    UserProfileCharge() {
      axios.post("/profile_config_get").then((response) => {
        //console.log(response);
        this.name = response.data.name;
        this.last_name = response.data.last_name;
        this.email = response.data.email;
        this.phone = response.data.phone;
        this.city = response.data.city;
        this.address = response.data.address;
        this.facebook_link = response.data.facebook_link;
        this.company_number = response.data.company_number;
        this.photo = response.data.photo;
        this.shop_url = response.data.shop_url;
      });
    },
    GeneralConfigCharge() {
      axios.post("/general_config_get").then((response) => {
        //console.log(response);
        this.name_ecommerce = response.data.name_ecommerce;
        this.company_billing = response.data.company_billing;
        this.voucher_logo = response.data.voucher_logo;
        this.type_of_environment = response.data.type_of_environment;
        this.continuous_paper_type = response.data.continuous_paper_type;
        this.ticket_default_format = response.data.ticket_default_format;
        this.invoice_default_format = response.data.invoice_default_format;
        this.default_document_type = response.data.default_document_type;
        this.default_payment_method = response.data.default_payment_method;
        this.printer = response.data.printer;
        this.optional_printer = response.data.optional_printer;
        this.printer_name = response.data.printer_name;
        this.css_config = response.data.default_css;
      });
    },
    GeneralConfigSave() {
      this.form_general_errors = [];
      if (!this.name_ecommerce) {
        this.form_general_errors.push("Ingrese un nombre para la tienda.");
        return;
      }
      let formData = new FormData();
      formData.append("name_ecommerce", this.name_ecommerce);
      formData.append("company_billing", this.company_billing);
      formData.append("voucher_logo", this.voucher_logo);
      formData.append("continuous_paper_type", this.continuous_paper_type);
      formData.append("type_of_environment", this.type_of_environment);
      formData.append("ticket_default_format", this.ticket_default_format);
      formData.append("invoice_default_format", this.invoice_default_format);
      formData.append("default_document_type", this.default_document_type);
      formData.append("default_payment_method", this.default_payment_method);
      formData.append("printer", this.printer);
      formData.append("optional_printer", this.optional_printer);
      formData.append("printer_name", this.printer_name);
      formData.append("default_css_config", this.css_config);
      axios.post("/general_config_save", formData).then((response) => {
        this.$store.commit("title", this.name_ecommerce);
        this.MessageOK();
      });
    },
    BsaleConfigCharge() {
      axios.post("/bsale_config_get").then((response) => {
        //console.log(response);
        this.url_documents = response.data.url_documents;
        this.invoice_id_letter = response.data.invoice_id_letter;
        this.invoice_id_thermal = response.data.invoice_id_thermal;
        this.ticket_id_letter = response.data.ticket_id_letter;
        this.ticket_id_thermal = response.data.ticket_id_thermal;
        this.quotation_id_letter = response.data.quotation_id_letter;
        this.token_production = response.data.token_production;
        this.token_certification = response.data.token_certification;
      });
    },
    BsaleConfigSave() {
      let formData = new FormData();
      formData.append("url_documents", this.url_documents);
      formData.append("invoice_id_letter", this.invoice_id_letter);
      formData.append("invoice_id_thermal", this.invoice_id_thermal);
      formData.append("ticket_id_letter", this.ticket_id_letter);
      formData.append("ticket_id_thermal", this.ticket_id_thermal);
      formData.append("quotation_id_letter", this.quotation_id_letter);
      formData.append("token_production", this.token_production);
      formData.append("token_certification", this.token_certification);
      axios.post("/bsale_config_save", formData).then((response) => {
        this.MessageOK();
      });
    },
  },
};
</script>
