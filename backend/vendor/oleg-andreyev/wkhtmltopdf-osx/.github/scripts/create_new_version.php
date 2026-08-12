<?php

# https://github.com/wkhtmltopdf/packaging/releases/download/0.12.2.1/wkhtmltox-0.12.2.1.macos-cocoa.pkg
# curl -vvv https://api.github.com/repos/wkhtmltopdf/wkhtmltopdf/releases

$json = json_decode(`curl -s https://api.github.com/repos/wkhtmltopdf/wkhtmltopdf/releases`, true);

$osxBuilds = array_filter(
    $json,
    static function ($v) {
        $assets = array_filter($v['assets'], static function ($v) {
            return strpos($v['name'], 'macos') !== false || strpos($v['name'], 'osx') !== false;
        });

        return count($assets) > 0;
    }
);

$data = array_map(
    static function ($v) {
        $assets = array_filter($v['assets'], static function ($v) {
            return strpos($v['name'], 'macos') !== false || strpos($v['name'], 'osx') !== false;
        });

        return [
            'tag_name' => $v['tag_name'],
            'assets'   => array_column($assets, 'browser_download_url')
        ];
    },
    $osxBuilds
);

$dir = __DIR__;

foreach (array_reverse($data) as $item) {
    var_dump($item);
    exec("rm -rf {$dir}/../../bin/*");

    foreach ($item['assets'] as $asset) {
        $path = parse_url($asset, PHP_URL_PATH);
        $filename = basename($path);

        preg_match('/(i386|x86-64)/', $filename, $match);
        $arch = isset($match[1]) ? ($match[1] === 'x86-64' ? 'amd64' : $match[1]) : 'amd64';

        exec("cd {$dir}/../../bin \
            && curl -L -s {$asset} -o {$filename} \
            && pkgutil --expand-full {$filename} ./dest \
            && (
                (
                    test -f ./dest/Payload/usr/local/share/wkhtmltox-installer/wkhtmltox.tar.gz \
                    && tar -xvf ./dest/Payload/usr/local/share/wkhtmltox-installer/wkhtmltox.tar.gz
                ) || (
                    test -f ./dest/Payload/usr/local/share/wkhtmltox-installer/app.tar.xz \
                    && tar -xf ./dest/Payload/usr/local/share/wkhtmltox-installer/app.tar.xz
                )
            ) \
            && cp bin/wkhtmltopdf wkhtmltopdf-{$arch} \
            && rm -rf bin include lib share dest {$filename} \
            && git add wkhtmltopdf-{$arch}
        ", $output, $exitCode);

        if ($exitCode !== 0) {
            echo $output;
            exit($exitCode);
        }
    }

    exec("git commit -m 'added {$item['tag_name']}' && git tag -f {$item['tag_name']}", $output, $exitCode);
    if ($exitCode !== 0) {
        echo $output;
        exit($exitCode);
    }
}

chdir(__DIR__);
