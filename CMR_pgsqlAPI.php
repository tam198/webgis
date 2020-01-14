<?php
    if(isset($_POST['functionname']))
    {
        $paPDO = initDB();
        $paSRID = '4326';
        $paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getGeoCMRToAjax')
            $aResult = getGeoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getInfoCMRToAjax')
            $aResult = getInfoCMRToAjax($paPDO, $paSRID, $paPoint);
        
        echo $aResult;
    
        closeDB($paPDO);
    }

    function initDB()
    {
        // Kết nối CSDL
        $paPDO = new PDO('pgsql:host=localhost;dbname=webGisProject;port=5432', 'postgres', 'postgres');
        return $paPDO;
    }
    function query($paPDO, $paSQLStr)
    {
        try
        {
            // Khai báo exception
            $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Sử đụng Prepare 
            $stmt = $paPDO->prepare($paSQLStr);
            // Thực thi câu truy vấn
            $stmt->execute();
            
            // Khai báo fetch kiểu mảng kết hợp
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            
            // Lấy danh sách kết quả
            $paResult = $stmt->fetchAll();   
            return $paResult;                 
        }
        catch(PDOException $e) {
            echo "Thất bại, Lỗi: " . $e->getMessage();
            return null;
        }       
    }
    function closeDB($paPDO)
    {
        // Ngắt kết nối
        $paPDO = null;
    }


    function getResult($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"CMR_adm1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"CMR_adm1\" where ST_Within('SRID=0".$paSRID.";".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }

    //highlight 
    function getGeoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"CMR_adm1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"vnm_adm1\" where ST_Within('SRID=0;".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }

    // ham xu li 
    function getInfoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"CMR_adm1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"CMR_adm1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        // $mySQLStr = "SELECT gid, name_1 from \"vnm_adm1\" where ST_Within('SRID=0;".$paPoint."'::geometry,geom)";
        $mySQLStr = "SELECT gid,ten,kieu_bt,vitri,trang_thai,nam_dexuat,tham_dinh,cap_bt,so_huu,quan_ly from \"khu_bao_ton\" where ST_Within('SRID=0;".$paPoint."'::geometry,geom)";
        $mySQLriver = "SELECT nameriver,lengthriver from \"river\" where ST_Within('SRID=0;".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        $resultRiver = query($paPDO,$mySQLriver);
        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>Mã vùng: '.$item['gid'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tên khu bảo tồn: '.$item['ten'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Kiểu bảo tồn: '.$item['kieu_bt'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Vị trí: '.$item['vitri'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Trạng thái: '.$item['trang_thai'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Năm đề xuất: '.$item['nam_dexuat'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Thẩm định: '.$item['tham_dinh'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Cấp bảo tồn: '.$item['cap_bt'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Sở hữu: '.$item['so_huu'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Quản lý: '.$item['quan_ly'].'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else if($resultRiver != null){
            $resFin = '<table>';
            foreach ($resultRiver as $item){
                $resFin = $resFin.'<tr><td>Tên sông: '.$item['nameriver'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Chiều dài sông: '.$item['lengthriver'].'</td></tr>';
           
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "null";
    }
?>