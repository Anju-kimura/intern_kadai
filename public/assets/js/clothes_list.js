function ClosetViewModel(apiUrl, loginUrl) {
  const self = this;

  // 画面に表示していて、変わる値だけを observable にする
  self.items = ko.observableArray([]);
  self.count = ko.observable(0);
  self.category = ko.observable('');
  self.color = ko.observable('');
  self.seasons = ko.observableArray([]);
  self.period = ko.observable('');
  self.sort = ko.observable('desc');
  self.loading = ko.observable(false);
  self.errorMessage = ko.observable('');

  // 画面に表示しない値は、普通の変数でよい
  let requestNo = 0;

  self.load = () => {
    const no = ++requestNo;
    self.loading(true);
    self.errorMessage('');

    const q = new URLSearchParams({
      category: self.category(),
      color: self.color(),
      period: self.period(),
      sort: self.sort(),
    });
    self.seasons().forEach((season) => q.append('seasons[]', season));

    fetch(apiUrl + '?' + q, { credentials: 'same-origin' })
      .then((res) => {
        if (res.status === 401) {
          location.href = loginUrl; // ログインが切れたので、ログイン画面へ移動する
          throw new Error('unauthorized');
        }
        if (!res.ok) throw new Error();
        return res.json();
      })
      .then((data) => {
        if (no !== requestNo) return; // 古いリクエストの結果は捨てる
        self.items(data.items);
        self.count(data.count);
      })
      .catch((error) => {
        if (error.message === 'unauthorized') return; // 移動中なので、メッセージは出さない
        if (no === requestNo) self.errorMessage('通信に失敗しました。');
      })
      .then(() => {
        if (no === requestNo) self.loading(false);
      });
  };

  // 条件を、すべて初期値に戻す
  self.reset = () => {
    self.category('');
    self.color('');
    self.seasons([]);
    self.period('');
    self.sort('desc');
  };

  // 条件のどれかが変わったら、自動で load() を呼ぶ
  ko.computed(() => {
    self.category(); self.color(); self.seasons(); self.period(); self.sort();
    return 1;
  }).extend({ notify: 'always', rateLimit: 50 }).subscribe(self.load);
}

const root = document.getElementById('closet');
const viewModel = new ClosetViewModel(root.dataset.apiUrl, root.dataset.loginUrl);
ko.applyBindings(viewModel, root);
viewModel.load(); // ページを開いたときの最初の一覧