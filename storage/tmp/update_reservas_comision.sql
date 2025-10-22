-- SQL patch to update reservas with recalculated commission and amounts
-- Review before running. This updates only a small set of ids identified by the dry-run report.

BEGIN TRANSACTION;

UPDATE reservas
SET comisionCanal = 480.00,
    montoReserva = 720.00,
    montoEmpresaAdministradora = 720.00,
    montoPropietario = 720.00,
    updated_at = CURRENT_TIMESTAMP
WHERE id = 4;

UPDATE reservas
SET comisionCanal = 480.00,
    montoReserva = 720.00,
    montoEmpresaAdministradora = 720.00,
    montoPropietario = 720.00,
    updated_at = CURRENT_TIMESTAMP
WHERE id = 5;

UPDATE reservas
SET comisionCanal = 420.00,
    montoReserva = 630.00,
    montoEmpresaAdministradora = 630.00,
    montoPropietario = 630.00,
    updated_at = CURRENT_TIMESTAMP
WHERE id = 7;

COMMIT;
