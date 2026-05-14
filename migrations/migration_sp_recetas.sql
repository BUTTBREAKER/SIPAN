DELIMITER //

DROP PROCEDURE IF EXISTS `sp_calcular_insumos_produccion` //

CREATE PROCEDURE `sp_calcular_insumos_produccion`(
    IN p_id_producto INT,
    IN p_cantidad_deseada INT
)
BEGIN
    DECLARE v_id_receta INT;
    DECLARE v_rendimiento INT;

    -- Obtener la receta del producto
    SELECT id, rendimiento INTO v_id_receta, v_rendimiento
    FROM recetas
    WHERE id_producto = p_id_producto
    LIMIT 1;

    -- Si no hay receta, salir sin hacer nada (o devolver error si prefieres)
    IF v_id_receta IS NULL THEN
        SELECT 'No existe receta para este producto' AS msg;
    ELSE
        -- Si rendimiento es 0 o nulo, evitar división por cero (por seguridad)
        IF v_rendimiento IS NULL OR v_rendimiento = 0 THEN
            SET v_rendimiento = 1;
        END IF;

        -- Calcular insumos necesarios
        -- La fórmula clásica: (cantidad_en_receta / rendimiento_receta) * cantidad_deseada
        SELECT 
            i.id AS id_insumo,
            i.nombre AS insumo_nombre,
            i.unidad_medida,
            i.stock_actual,
            i.precio_unitario,
            i.costo_unitario,
            ri.cantidad AS cantidad_receta_base,
            (ri.cantidad / v_rendimiento) * p_cantidad_deseada AS cantidad_necesaria,
            ((ri.cantidad / v_rendimiento) * p_cantidad_deseada) * i.costo_unitario AS costo_estimado,
            -- Chequeo rápido de disponibilidad
            IF(i.stock_actual >= ((ri.cantidad / v_rendimiento) * p_cantidad_deseada), 'SI', 'NO') AS suficiente
        FROM receta_insumos ri
        JOIN insumos i ON ri.id_insumo = i.id
        WHERE ri.id_receta = v_id_receta;
    END IF;

END //

DELIMITER ;
