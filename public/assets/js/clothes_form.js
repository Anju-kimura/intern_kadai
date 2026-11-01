function ClothesFormViewModel(initial, rules, options) {
  const self = this;

  // 画面に表示していて、変わる値だけを observable にする
  self.name = ko.observable(initial.name);
  self.category = ko.observable(initial.category);
  self.brand = ko.observable(initial.brand);
  self.color = ko.observable(initial.color);
  self.purchaseDate = ko.observable(initial.purchase_date);
  self.purchaseUnknown = ko.observable(initial.purchase_unknown);
  self.seasons = ko.observableArray(initial.seasons);
  self.memo = ko.observable(initial.memo);
  self.previewUrl = ko.observable(initial.image_url);
  self.errors = ko.observable({});
  self.submitting = ko.observable(false);

  // 画面に表示しない値は、普通の変数でよい
  let csrfToken = options.csrfToken; // 送信のたびに、サーバーから最新のものを受け取る
  let imageFile = null;
  let objectUrl = null;

  const imageMessage = '画像は、' + rules.extensions.join('・') + '形式の、'
    + Math.round(rules.max_size / 1024 / 1024) + 'MB以内のファイルを選んでください';

  const clearImage = () => {
    if (objectUrl !== null) URL.revokeObjectURL(objectUrl);
    objectUrl = null;
    imageFile = null;
    self.previewUrl(initial.image_url);
  };

  // 画像を選んだら、すぐにプレビューを出す。画面側のチェックは使いやすさのため
  self.onImageChange = (data, event) => {
    const file = event.target.files[0];
    clearImage();
    if (!file) return true;

    if (!rules.mimes.includes(file.type) || file.size > rules.max_size) {
      event.target.value = '';
      self.errors({ image: imageMessage });
      return true;
    }

    self.errors({});
    imageFile = file;
    objectUrl = URL.createObjectURL(file);
    self.previewUrl(objectUrl);
    return true;
  };

  self.save = () => {
    if (self.submitting()) return;

    const fd = new FormData();
    fd.append(options.csrfKey, csrfToken);
    fd.append('id', initial.id);
    fd.append('name', self.name());
    fd.append('category', self.category());
    fd.append('brand', self.brand());
    fd.append('color', self.color());
    fd.append('purchase_date', self.purchaseDate());
    if (self.purchaseUnknown()) fd.append('purchase_unknown', '1');
    self.seasons().forEach((season) => fd.append('seasons[]', season));
    fd.append('memo', self.memo());
    if (imageFile) fd.append('image', imageFile);

    let moved = false;
    self.submitting(true);
    self.errors({});

    fetch(options.saveUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
      .then((res) => {
        if (res.status === 400 || res.status === 401) throw new Error(); // トークン不正、またはログイン切れ
        return res.json();
      })
      .then((data) => {
        if (data.token) csrfToken = data.token;
        if (!data.ok) {
          self.errors(data.errors || { _all: '保存できませんでした。' });
          return;
        }
        moved = true;
        location.href = options.backUrl;
      })
      .catch(() => self.errors({ _all: '通信に失敗しました。ページを読み込み直してください。' }))
      .then(() => {
        if (!moved) self.submitting(false);
      });
  };
}

const formRoot = document.getElementById('clothes-form');
const formViewModel = new ClothesFormViewModel(
  JSON.parse(formRoot.dataset.initial),
  JSON.parse(formRoot.dataset.rules),
  {
    saveUrl: formRoot.dataset.saveUrl,
    backUrl: formRoot.dataset.backUrl,
    csrfKey: formRoot.dataset.csrfKey,
    csrfToken: formRoot.dataset.csrfToken,
  }
);
ko.applyBindings(formViewModel, formRoot);