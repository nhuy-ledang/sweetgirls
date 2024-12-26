import { ElementRef } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormGroup } from '@angular/forms';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { Api } from '../../../../@core/services';
import { LanguagesRepository, ModulesRepository as CfModulesRepository } from '../../../../@core/repositories';
import { AppForm } from '../../../../app.base';
import { CategoriesRepository as PdCategoriesRepository } from '../../../products/shared/services';
import { CategoriesRepository, ModulesRepository, PagesRepository } from '../../shared/services';

export class BaseModuleFormComponent extends AppForm {
  modal: any;
  onSuccess: any;
  info: any|boolean;
  controls: {
    name?: AbstractControl,
    title?: AbstractControl,
    sub_title?: AbstractControl,
    code?: AbstractControl,
    layout?: AbstractControl,
    tile?: AbstractControl,
    short_description?: AbstractControl,
    description?: AbstractControl,
    properties?: any,
    table_contents?: AbstractControl,
    table_images?: AbstractControl,
    image?: AbstractControl,
    attach?: AbstractControl,
    menu_text?: AbstractControl,
    btn_text?: AbstractControl,
    btn_link?: AbstractControl,
  };
  propForm: FormGroup;
  propControls: {
    source?: AbstractControl,
    source_ids?: AbstractControl,
    source_names?: AbstractControl,
    category_id?: AbstractControl, // Will remove, change to source_ids
    page_id?: AbstractControl,
    primaryColor?: AbstractControl,
    secondaryColor?: AbstractControl,
    successColor?: AbstractControl,
    imgSize?: AbstractControl,
    bgColor?: AbstractControl,
    titleColor?: AbstractControl,
    titleAlign?: AbstractControl,
    subTitleColor?: AbstractControl,
    textColor?: AbstractControl,
    bgImg?: AbstractControl,
    bgSize?: AbstractControl,
    bgFixed?: AbstractControl,
    bgParallax?: AbstractControl,
    cont?: AbstractControl,
    mt?: AbstractControl,
    mb?: AbstractControl,
    pt?: AbstractControl,
    pb?: AbstractControl,
    mtxl?: AbstractControl,
    mbxl?: AbstractControl,
    ptxl?: AbstractControl,
    pbxl?: AbstractControl,
    youtube?: AbstractControl,
    col?: AbstractControl,
    spacingCol?: AbstractControl,
    row?: AbstractControl,
    textRow?: AbstractControl,
    colMb?: AbstractControl,
    spacingColMb?: AbstractControl,
    rowMb?: AbstractControl,
    textRowMb?: AbstractControl,
    imgFrame?: AbstractControl,
    imgFrameMb?: AbstractControl,
    button?: AbstractControl,
    textButton?: AbstractControl,
    linkButton?: AbstractControl,
    btnModuleLink?: AbstractControl,
    menu?: AbstractControl,
    textMenu?: AbstractControl,
    reverse?: AbstractControl,
    btnStyle?: AbstractControl,
    buttonColor?: AbstractControl,
    buttonHoverColor?: AbstractControl,
    textBtnColor?: AbstractControl,
    textBtnHoverColor?: AbstractControl,
    buttonAlign?: AbstractControl,
    dot?: AbstractControl,
    dotStyle?: AbstractControl,
    dotColor?: AbstractControl,
    dotActiveColor?: AbstractControl,
    textDotColor?: AbstractControl,
    textDotActiveColor?: AbstractControl,
    dotAlign?: AbstractControl,
    dotMb?: AbstractControl,
    dotHeight?: AbstractControl,
    arrow?: AbstractControl,
    arrowHide?: AbstractControl,
    arrowStyle?: AbstractControl,
    arrowColor?: AbstractControl,
    arrowHoverColor?: AbstractControl,
    textArrowColor?: AbstractControl,
    textArrowHoverColor?: AbstractControl,
    arrowRight?: AbstractControl,
    arrowBottom?: AbstractControl,
  };
  tbcForm: AbstractControl|any;
  imgForm: AbstractControl|any;
  files1: any[] = [];
  fileOpts1: any[] = [];
  filePaths1: any[] = [];
  files2: any[] = [];
  fileOpts2: any[] = [];
  filePaths2: any[] = [];
  files3: any[] = [];
  fileOpts3: any[] = [];
  filePaths3: any[] = [];
  tbcAttaches: any[] = [];
  imgs: any[] = [];
  imgOpts: any[] = [];
  imgPaths: any[] = [];
  // editorOpt: any = {toolbar1: false};
  editorOpt: any = {};
  isCollapsed = true;
  colors = [
    {id: 'primary', name: 'Màu chính'},
    {id: 'secondary', name: 'Màu thứ 2'},
    {id: 'success', name: 'Màu thứ 3'},
    {id: 'white', name: 'White'},
    {id: 'black', name: 'Black'},
  ];
  configs = {
    template_url: false,
    short_description: false,
    description: false,
    image: false,
    attach: false,
    table_contents: false,
    table_images: false,
    properties: {category_id: false, page_id: false, youtube: false, reverse_md: false},
  };
  sourceList: any[] = [
    {id: 'pages', name: 'Danh sách trang'},
    {id: 'pd_manufacturers', name: 'Thương hiệu sản phẩm'},
    {id: 'pd_categories', name: 'Danh mục sản phẩm'},
    {id: 'pd_products', name: 'Sản phẩm'},
  ];
  cfModuleData: { loading: boolean, modules: any[], pages: any[], buttons: any[], dots: any[], arrows: any[] } = {loading: false, modules: [], pages: [], buttons: [], dots: [], arrows: []};
  // moduleData: { loading: boolean, items: any[] } = {loading: false, items: []};
  moduleSelected: {id: string, name: string, configs: any, layouts: any[], tiles: any[], previews: any, cf_data: any};
  categoryData: {loading: boolean, items: any[]} = {loading: false, items: []};
  pageData: {loading: boolean, items: any[]} = {loading: false, items: []};
  pdCategoryData: {loading: boolean, items: any[]} = {loading: false, items: []};
  categoryList: any[] = [];
  listModules: {loading: boolean, items: any[]} = {loading: false, items: []};
  layouts: any[] = [];
  tiles: any[] = [];
  previews: any = {};
  widgetPreview: string = '';
  buttonPreview: string = '';
  dotPreview: string = '';
  arrowPreview: string = '';

  constructor(router: Router, security: Security, state: GlobalState, repository: Api, fb: FormBuilder, languages: LanguagesRepository,
              protected _cfModules: CfModulesRepository, protected _modules: ModulesRepository, protected _categories: CategoriesRepository, protected _pages: PagesRepository, protected _pdCategories: PdCategoriesRepository) {
    super(router, security, state, repository, languages);
    this.propForm = fb.group({
      source: [''],
      source_ids: [''],
      source_names: [''],
      category_id: [''],
      page_id: [''],
      primaryColor: [''],
      secondaryColor: [''],
      successColor: [''],
      imgSize: [''],
      bgColor: [''],
      titleColor: [''],
      titleAlign: [''],
      subTitleColor: [''],
      textColor: [''],
      bgImg: [''],
      bgSize: [''],
      bgFixed: [''],
      bgParallax: [''],
      cont: [''],
      mt: [''],
      mb: [''],
      pt: [''],
      pb: [''],
      mtxl: [''],
      mbxl: [''],
      ptxl: [''],
      pbxl: [''],
      youtube: [''],
      col: [''],
      spacingCol: [''],
      row: [''],
      textRow: [''],
      colMb: [''],
      spacingColMb: [''],
      rowMb: [''],
      textRowMb: [''],
      imgFrame: [''],
      imgFrameMb: [''],
      button: [''],
      textButton: [''],
      linkButton: [''],
      btnModuleLink: [''],
      menu: [''],
      textMenu: [''],
      reverse: [''],
      btnStyle: [''],
      buttonColor: [''],
      buttonHoverColor: [''],
      textBtnColor: [''],
      textBtnHoverColor: [''],
      buttonAlign: [''],
      dot: [''],
      dotStyle: [''],
      dotColor: [''],
      dotActiveColor: [''],
      textDotColor: [''],
      textDotActiveColor: [''],
      dotMb: [''],
      dotAlign: [''],
      dotHeight: [''],
      arrow: [''],
      arrowHide: [''],
      arrowStyle: [''],
      arrowColor: [''],
      arrowHoverColor: [''],
      textArrowColor: [''],
      textArrowHoverColor: [''],
      arrowRight: [''],
      arrowBottom: [''],
    });
    this.propControls = this.propForm.controls;
  }

  // Will remove
  protected setCategoryList(): void {
    if (this.propControls.source.value === 'page') {
      this.categoryList = _.cloneDeep(this.categoryData.items);
    } else if (this.propControls.source.value === 'product') {
      this.categoryList = _.cloneDeep(this.pdCategoryData.items);
    } else {
      this.categoryList = [];
    }
    setTimeout(() => {
      if (this.propControls.category_id.value) {
        const category: any = _.find(this.categoryList, {id: parseInt(this.propControls.category_id.value, 0)});
        this.propControls.category_id.setValue(category ? category.id : '');
      }
    });
  }

  // Will remove
  protected getAllCategory(): void {
    this.categoryData.loading = true;
    this._categories.all().then((res: any) => {
      this.categoryData.loading = false;
      this.categoryData.items = res.data;
      this.setCategoryList();
    }), (errors: any) => {
      this.categoryData.loading = false;
      console.log(errors);
    };
  }

  protected getAllPage(): void {
    this.pageData.loading = true;
    this._pages.all().then((res: any) => {
      this.pageData.loading = false;
      this.pageData.items = res.data;
    }), (errors: any) => {
      this.pageData.loading = false;
      console.log(errors);
    };
  }

  /*protected getAllModules(): void {
    this._modules.get({pageSize: 100}).then((res: any) => {
      this.moduleData.loading = false;
      this.moduleData.items = res.data;
      this.onModuleChange();
    }), (errors: any) => {
      this.moduleData.loading = false;
      console.log(errors);
    };
  }*/

  protected getCfModules(): void {
    this.cfModuleData.loading = true;
    this._cfModules.all().then((res: any) => {
      console.log('getCfModules', res.data);
      this.cfModuleData.loading = false;
      const cfModuleData = _.extend(this.cfModuleData, res.data);
      /*_.forEach(cfModuleData.modules, (module: any) => {
        const previews: any = {};
        previews['layout1'] = module.configs && module.configs.template_url ? module.configs.template_url : '';
        if (module.layouts) _.forEach(module.layouts, (layout: any) => {
          previews[layout.id] = layout.preview;
        });
        module.previews = previews;
      });*/
      this.cfModuleData = cfModuleData;
      this.onCfModuleChange();
    }), (errors: any) => {
      this.cfModuleData.loading = false;
      console.log(errors);
    };
  }

  protected getListModules(id?: any, info?: any): void {
    this.repository.get({data: {page_id: id}}).then((res: any) => {
      this.listModules.loading = false;
      this.listModules.items = res.data;
    }), (errors: any) => {
      this.listModules.loading = false;
      console.log(errors);
    };
  }

  protected getAllPdCategory(): void {
    this.pdCategoryData.loading = true;
    this._pdCategories.all().then((res: any) => {
      this.pdCategoryData.loading = false;
      this.pdCategoryData.items = res.data;
      this.setCategoryList();
    }), (errors: any) => {
      this.pdCategoryData.loading = false;
      console.log(errors);
    };
  }

  protected updateConfigs(configs: any): void {
    _.each(this.configs, (val, key) => {
      this.configs[key] = configs && configs.hasOwnProperty(key) ? configs[key] : true;
    });
  }

  protected setProperties(info: any): void {
    // Set table_contents
    this.tbcForm.controls = [];
    if (!info.table_contents) info.table_contents = [];
    _.forEach(info.table_contents, (item) => this.addRow(item));
    // Set table_images
    this.imgForm.controls = [];
    this.imgs = [];
    this.imgOpts = [];
    this.imgPaths = [];
    if (!info.table_images) info.table_images = [];
    _.forEach(info.table_images, (item) => this.addImgRow(item));
    // Set properties
    if (typeof info.properties === 'string') info.properties = JSON.parse(info.properties);
    if (!info.properties) info.properties = {};
    console.log('properties', info.properties);
    _.each(this.propControls, (val, key) => {
      if (this.propControls.hasOwnProperty(key)) this.propControls[key].setValue(info.properties.hasOwnProperty(key) && info.properties[key] !== null ? info.properties[key] : '');
    });
    if (this.propControls.menu && this.propControls.menu.value) {
      if (!this.controls.menu_text.value && this.propControls.textMenu && this.propControls.textMenu.value) this.controls.menu_text.setValue(this.propControls.textMenu.value);
    }
    if (this.propControls.button && this.propControls.button.value) {
      if (!this.controls.btn_text.value && this.propControls.textButton && this.propControls.textButton.value) this.controls.btn_text.setValue(this.propControls.textButton.value);
      if (!this.controls.btn_link.value && this.propControls.linkButton && this.propControls.linkButton.value) this.controls.btn_link.setValue(this.propControls.linkButton.value);
    }
    // Set widget preview
    let widgetPreview: string = '';
    if (info.tile) {
      const temps = info.tile.split('_');
      const widget_code = temps[0];
      const class_id = temps.length > 1 ? parseInt(temps[1], 0) : 0;
      if (info.widget && info.widget.cf_data && info.widget.cf_data.classes) {
        const cl: any = _.find(info.widget.cf_data.classes, {id: class_id});
        if (cl) widgetPreview = cl.preview;
      }
    }
    this.widgetPreview = widgetPreview;
  }

  show(info: any): void {
    this.isCollapsed = true;
    this.resetForm(this.form);
    this.resetForm(this.propForm);
    this.info = false;
    this.tbcForm.controls = [];
    this.files1 = [];
    this.fileOpts1 = [];
    this.filePaths1 = [];
    this.files2 = [];
    this.fileOpts2 = [];
    this.filePaths2 = [];
    this.files3 = [];
    this.fileOpts3 = [];
    this.filePaths3 = [];
    this.tbcAttaches = [];
    this.imgForm.controls = [];
    this.imgs = [];
    this.imgOpts = [];
    this.imgPaths = [];
    this.setInfo(info);
    if (info.id) this.getInfo(info.id, {embed: 'descs'});
    this.tbcForm.updateValueAndValidity();
    this.imgForm.updateValueAndValidity();
    // if (!this.cfModuleData.modules.length || !this.cfModuleData.pages.length) this.getCfModules();
    setTimeout(() => {
      if (!this.categoryData.items.length) this.getAllCategory();
    }, 1000);
    setTimeout(() => {
      if (!this.pageData.items.length) this.getAllPage();
    }, 1500);
    if (this.controls.code.value === 'pd_category') {
      setTimeout(() => {
        if (!this.pdCategoryData.items.length) this.getAllPdCategory();
      }, 2000);
    }
    this.modal.show();
  }

  hide(): void {
    this.attachFile = null;
    this.bgFile = null;
    this.bgSelected = null;
    this.bgOpts.thumb_url = '';
    this.propForm.reset();
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  protected getSubmitParams(params): any {
    const newParams = _.cloneDeep(params);
    if (this.fileSelected) {
      newParams.file_path = this.fileSelected.path;
    } else if (this.file) {
      newParams.file = this.file;
    } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
    const table_contents = [];
    _.forEach(params.table_contents, (item: any) => {
      delete item['attach_url'];
      table_contents.push(item);
    });
    newParams['table_contents'] = table_contents;
    for (let i = 0; i < this.files1.length; i++) {
      newParams['file_' + i] = this.files1[i];
    }
    for (let i = 0; i < this.filePaths1.length; i++) {
      newParams['filepath_' + i] = this.filePaths1[i] ? this.filePaths1[i] : '';
    }
    for (let i = 0; i < this.files2.length; i++) {
      newParams['file2_' + i] = this.files2[i];
    }
    for (let i = 0; i < this.filePaths2.length; i++) {
      newParams['filepath2_' + i] = this.filePaths2[i] ? this.filePaths2[i] : '';
    }
    for (let i = 0; i < this.files3.length; i++) {
      newParams['file3_' + i] = this.files3[i];
    }
    for (let i = 0; i < this.filePaths3.length; i++) {
      newParams['filepath3_' + i] = this.filePaths3[i] ? this.filePaths3[i] : '';
    }
    for (let i = 0; i < this.tbcAttaches.length; i++) {
      newParams['attache_' + i] = this.tbcAttaches[i];
    }
    const table_images = [];
    _.forEach(params.table_images, (item: any) => table_images.push(item));
    newParams['table_images'] = table_images;
    for (let i = 0; i < this.imgs.length; i++) {
      newParams['img_' + i] = this.imgs[i];
    }
    for (let i = 0; i < this.imgPaths.length; i++) {
      newParams['imgpath_' + i] = this.imgPaths[i] ? this.imgPaths[i] : '';
    }
    if (this.bgSelected) {
      newParams.prop_filepath = this.bgSelected.path;
    } else if (this.bgFile) {
      newParams.prop_file = this.bgFile;
    } else if (!this.bgOpts.thumb_url) newParams.properties.bgImg = '';
    if (this.attachFile) newParams['attach_file'] = this.attachFile;

    return newParams;
  }

  onChangeSource($event?: any): void {
    if ($event) {
      // this.propControls.category_id.setValue('');
      this.propControls.source_ids.setValue('');
      this.propControls.source_names.setValue('');
    }
    /*if (this.propControls.source.value === 'page') {
      if (!this.categoryData.items.length) this.getAllCategory();
      else this.setCategoryList();
    } else if (this.propControls.source.value === 'product') {
      if (!this.pdCategoryData.items.length) this.getAllPdCategory();
      else this.setCategoryList();
    } else {
      this.setCategoryList();
    }*/
  }

  // loadModuleData(): void {}

  // onIsOverwriteChange(): void {}
  selectModule(module?: {id: string, name: string, configs: any, layouts: any[], tiles: any[], previews: any, cf_data: any}): void {
    if (!module) return;
    // console.log(module);
    this.moduleSelected = module;
    const configs: any = this.moduleSelected.cf_data ? this.moduleSelected.cf_data.configs : false;
    this.updateConfigs(configs);
    // Preview
    const previews: any = {};
    previews['layout1'] = configs && configs.template_url ? configs.template_url : '';
    if (this.moduleSelected.cf_data && this.moduleSelected.cf_data.layouts) _.forEach(this.moduleSelected.cf_data.layouts, (layout: any) => {
      previews[layout.id] = layout.preview;
    });
    this.previews = previews;
    let layouts = this.moduleSelected.cf_data && this.moduleSelected.cf_data.layouts ? this.moduleSelected.cf_data.layouts : [];
    const tmp = _.find(layouts, {id: 'layout1'});
    if (!tmp) layouts = [{id: 'layout1', name: 'Layout 1', preview: previews['layout1']}].concat(layouts);
    this.layouts = layouts;
    this.tiles = this.moduleSelected.cf_data && this.moduleSelected.cf_data.tiles ? this.moduleSelected.cf_data.tiles : [];
    // console.log(this.layouts, this.tiles);
  }

  onModuleChange($event?: any): void {
  }

  onCfModuleChange($event?: any): void {
    console.log('onCfModuleChange', this.controls.code.value);
    // Old module - will remove
    if (this.controls.code.value === 'category') this.propControls.source.setValue('services');
    if (this.controls.code.value === 'pd_category') this.propControls.source.setValue('product');
    // Set default
    if (this.configs.properties.category_id) {
      if (!this.propControls.source.value) this.propControls.source.setValue(this.sourceList[0].id);
    }
    this.onChangeSource();
  }

  onChangeStyle(): void {
    if (this.cfModuleData.buttons) {
      const md = _.find(this.cfModuleData.buttons, {id: this.propControls.btnStyle.value});
      if (md) {
        this.buttonPreview = md.preview;
      } else {
        this.buttonPreview = null;
      }
    } else {
      this.buttonPreview = null;
    }

    if (this.cfModuleData.dots) {
      const md = _.find(this.cfModuleData.dots, {id: this.propControls.dotStyle.value});
      if (md) {
        this.dotPreview = md.preview;
      } else {
        this.dotPreview = null;
      }
    } else {
      this.dotPreview = null;
    }

    if (this.cfModuleData.arrows) {
      const md = _.find(this.cfModuleData.arrows, {id: this.propControls.arrowStyle.value});
      if (md) {
        this.arrowPreview = md.preview;
      } else {
        this.arrowPreview = null;
      }
    } else {
      this.arrowPreview = null;
    }
  }

  addRow(item?: any): void {
    this.tbcForm.push(this.fb.group({
      name: [item && item.name ? item.name : ''],
      short_description: [item && item.short_description ? item.short_description : ''],
      description: [item && item.description ? item.description : ''],
      image: [item && item.image ? item.image : ''],
      image2: [item && item.image2 ? item.image2 : ''],
      image3: [item && item.image3 ? item.image3 : ''],
      link: [item && item.link ? item.link : ''],
      attach: [item && item.attach ? item.attach : ''],
      attach_url: [item && item.attach_url ? item.attach_url : ''],
    }));
    this.files1.push(null);
    this.fileOpts1.push({thumb_url: item && item.thumb_url ? item.thumb_url : ''});
    this.filePaths1.push(null);
    this.files2.push(null);
    this.fileOpts2.push({thumb_url: item && item.thumb_url2 ? item.thumb_url2 : ''});
    this.filePaths2.push(null);
    this.files3.push(null);
    this.fileOpts3.push({thumb_url: item && item.thumb_url3 ? item.thumb_url3 : ''});
    this.filePaths3.push(null);
    this.tbcAttaches.push(null);
  }

  removeRow(i: number): void {
    this.tbcForm.removeAt(i);
    this.files1.splice(i, 1);
    this.fileOpts1.splice(i, 1);
    this.filePaths1.splice(i, 1);
    this.files2.splice(i, 1);
    this.fileOpts2.splice(i, 1);
    this.filePaths2.splice(i, 1);
    this.files3.splice(i, 1);
    this.fileOpts3.splice(i, 1);
    this.filePaths3.splice(i, 1);
    this.tbcAttaches.splice(i, 1);
  }

  onImageSelected(i: number, file: File|any): void {
    if (file.type === 'select') {
      this.files1[i] = null;
      this.filePaths1[i] = file.path;
    } else {
      this.files1[i] = file;
      this.filePaths1[i] = null;
    }
  }

  onImageDeleted(i: number, event?: any): void {
    this.files1[i] = null;
    this.filePaths1[i] = null;
  }

  onImageSelected2(i: number, file: File|any): void {
    if (file.type === 'select') {
      this.files2[i] = null;
      this.filePaths2[i] = file.path;
    } else {
      this.files2[i] = file;
      this.filePaths2[i] = null;
    }
  }

  onImageDeleted2(i: number, event?: any): void {
    this.files2[i] = null;
    this.filePaths2[i] = null;
  }

  onImageSelected3(i: number, file: File|any): void {
    if (file.type === 'select') {
      this.files3[i] = null;
      this.filePaths3[i] = file.path;
    } else {
      this.files3[i] = file;
      this.filePaths3[i] = null;
    }
  }

  onImageDeleted3(i: number, event?: any): void {
    this.files3[i] = null;
    this.filePaths3[i] = null;
  }

  // Table images
  addImgRow(item?: any): void {
    this.imgForm.push(this.fb.group({
      name: [item && item.name ? item.name : ''],
      short_description: [item && item.short_description ? item.short_description : ''],
      link: [item && item.link ? item.link : ''],
      description: [item && item.description ? item.description : ''],
      image: [item && item.image ? item.image : ''],
    }));
    this.imgs.push(null);
    this.imgOpts.push({thumb_url: item && item.thumb_url ? item.thumb_url : ''});
    this.imgPaths.push(null);
  }

  removeImgRow(i: number): void {
    this.imgForm.removeAt(i);
    this.imgs.splice(i, 1);
    this.imgOpts.splice(i, 1);
    this.imgPaths.splice(i, 1);
  }

  onImgSelected(i: number, file: File|any): void {
    if (file.type === 'select') {
      this.imgs[i] = null;
      this.imgPaths[i] = file.path;
    } else {
      this.imgs[i] = file;
      this.imgPaths[i] = null;
    }
  }

  onImgDeleted(i: number, event?: any): void {
    this.imgs[i] = null;
    this.imgPaths[i] = null;
  }

  // End table images

  // Background Image
  bgOpts: any = {thumb_url: '', aspect_ratio: '16by9'};
  bgFile: File = null;
  bgSelected: {path: string} = null;

  onBgSelected(event: File|any): void {
    if (event.type === 'select') {
      this.bgSelected = event;
      this.bgFile = null;
    } else {
      this.bgSelected = null;
      this.bgFile = event;
    }
  }

  onBgDeleted(event): void {
    this.bgFile = null;
    this.bgSelected = null;
    this.bgOpts.thumb_url = '';
  }

  _attachFile: ElementRef;
  attachFile: File = null;

  onAttach(): void {
    const files = this._attachFile.nativeElement.files;
    this.attachFile = files.length ? files[0] : null;
  }

  removeAttach(): void {
    if (this.info) this.info.attach_url = '';
    this.controls.attach.setValue('');
  }

  onTbcAttach(i: number, $event: any): void {
    const files = $event.currentTarget.files;
    this.tbcAttaches[i] = files.length ? files[0] : null;
  }

  removeTbcAttach(i: number, $event: any, controls: any): void {
    controls.attach_url.setValue('');
    controls.attach.setValue('');
  }
}
