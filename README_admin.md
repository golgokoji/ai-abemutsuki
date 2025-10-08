管理画面の実装に関しては、README_adminの内容を参考にしてください。


# 🧭 Filament 管理メニューにリソースを追加する際のチェックリスト

Copilot が `AdminPanelProvider` の編集を失念しやすいため、  
**新しい Resource を追加する際は必ず以下を確認してください。**

---

## ✅ 1. Resource 側の設定

例：`app/Filament/Resources/PayzPendingGrantResource.php`

```php
<?php

namespace App\Filament\Resources;

use App\Models\PayzPendingGrant;
use Filament\Resources\Resource;

class PayzPendingGrantResource extends Resource
{
    protected static ?string $model = PayzPendingGrant::class;

    // ナビゲーションに出す設定
    protected static ?string $navigationLabel = 'Payz保留付与';
    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static ?string $navigationGroup = '課金 / クレジット';

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayzPendingGrants::route('/'),
            'create' => Pages\CreatePayzPendingGrant::route('/create'),
            'edit'   => Pages\EditPayzPendingGrant::route('/{record}/edit'),
        ];
    }
}
```

> **ポイント**
> - `getPages()` に `'index'` ページを必ず含める  
> - `shouldRegisterNavigation()` を上書きしている場合は `true` を返す  
> - これで Resource 側の準備は完了

---

## ✅ 2. `AdminPanelProvider` の登録を忘れない！（Copilot がよく抜ける）

例：`app/Providers/Filament/AdminPanelProvider.php`

```php
use Filament\Panel;
use App\Filament\Resources\PayzPendingGrantResource;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')

        // 👇 ここを忘れずに！
        ->resources([
            PayzPendingGrantResource::class,
        ])

        // または自動検出を有効化（この場合は上記 resources 不要）
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets');
}
```

> **補足**
> - どちらか一方でOK  
>   - 明示登録（`->resources([...])`）  
>   - 自動検出（`->discoverResources(...)`）  
> - 名前空間がズレている／フォルダ外の場合は **明示登録** を推奨

---

## 🧩 よくあるエラーと解決法

| エラー内容 | 原因 | 解決法 |
|---|---|---|
| `RouteNotFoundException: Route [xxx.index] not defined` | AdminPanelProvider に未登録 / `index` ページ未定義 | 上記 ①② を再確認 |
| Resource を作ってもメニューに出ない | `shouldRegisterNavigation()` が `false` | `true` に修正 |
| グループ名が英語のまま | `$navigationGroup` 未設定 | 任意の日本語ラベルを設定 |

---

## 🧪 テスト手順

```bash
php artisan optimize:clear
php artisan queue:restart  # キュー運用時のみ
```

その後、管理画面をリロードしてメニューに追加したリソースが表示されているか確認。

---

## ✏️ 開発メモ（Copilot 向け注意）

- 新しい Resource を生成したら、**必ず `AdminPanelProvider` を編集対象に含めること**。  
- `discoverResources()` で自動認識される構成でも、**パス外／名前空間不一致**は自動反映されないため、必要に応じて **`->resources([...])` で明示登録**すること。
