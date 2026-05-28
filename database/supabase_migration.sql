-- ============================================================
-- TradeRitual - Supabase SQL Migration
-- Run this in the Supabase SQL Editor to create all tables.
-- ============================================================

-- Enable UUID extension (usually already enabled in Supabase)
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ─────────────────────────────────────────────
-- Table 1: profiles
-- Stores user profile data and gamification scores
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.profiles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL UNIQUE REFERENCES auth.users(id) ON DELETE CASCADE,
    username VARCHAR(255),
    discipline_score INTEGER NOT NULL DEFAULT 0,
    default_max_loss DECIMAL(12, 2) NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Index for fast user lookups
CREATE INDEX IF NOT EXISTS idx_profiles_user_id ON public.profiles(user_id);

-- ─────────────────────────────────────────────
-- Table 2: daily_rituals
-- Pre-Market & Post-Market Check-ins
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.daily_rituals (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES public.profiles(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    sleep_hours INTEGER NOT NULL,
    pre_mood INTEGER NOT NULL CHECK (pre_mood BETWEEN 1 AND 5),
    max_loss_limit DECIMAL(12, 2) NOT NULL,
    post_mood INTEGER CHECK (post_mood BETWEEN 1 AND 5),
    followed_plan BOOLEAN,
    daily_notes TEXT,

    -- Enforce one ritual per user per day
    UNIQUE(user_id, date)
);

-- Composite index for fast lookups
CREATE INDEX IF NOT EXISTS idx_daily_rituals_user_date ON public.daily_rituals(user_id, date);

-- ─────────────────────────────────────────────
-- Table 3: trades
-- Core trade journal entries
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.trades (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES public.profiles(id) ON DELETE CASCADE,
    ritual_id UUID NOT NULL REFERENCES public.daily_rituals(id) ON DELETE CASCADE,
    pair VARCHAR(20) NOT NULL,
    direction VARCHAR(10) NOT NULL CHECK (direction IN ('Long', 'Short')),
    entry_price DECIMAL(16, 5) NOT NULL,
    stop_loss DECIMAL(16, 5) NOT NULL,
    take_profit DECIMAL(16, 5) NOT NULL,
    exit_price DECIMAL(16, 5),
    lot_size DECIMAL(10, 2) NOT NULL,
    pnl DECIMAL(16, 2),
    tags JSONB NOT NULL DEFAULT '[]'::JSONB,
    screenshot_entry VARCHAR(500),
    screenshot_exit VARCHAR(500),
    trade_notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Indexes for trade queries
CREATE INDEX IF NOT EXISTS idx_trades_user_id ON public.trades(user_id);
CREATE INDEX IF NOT EXISTS idx_trades_ritual_id ON public.trades(ritual_id);
CREATE INDEX IF NOT EXISTS idx_trades_user_created ON public.trades(user_id, created_at);

-- ─────────────────────────────────────────────
-- Row Level Security (RLS) Policies
-- Users can only access their own data
-- ─────────────────────────────────────────────

-- Enable RLS on all tables
ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.daily_rituals ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.trades ENABLE ROW LEVEL SECURITY;

-- Profiles: Users can read/write only their own profile
CREATE POLICY "Users can view own profile"
    ON public.profiles FOR SELECT
    USING (auth.uid() = user_id);

CREATE POLICY "Users can insert own profile"
    ON public.profiles FOR INSERT
    WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update own profile"
    ON public.profiles FOR UPDATE
    USING (auth.uid() = user_id);

-- Daily Rituals: Users can read/write only their own rituals
CREATE POLICY "Users can view own rituals"
    ON public.daily_rituals FOR SELECT
    USING (user_id IN (SELECT id FROM public.profiles WHERE user_id = auth.uid()));

CREATE POLICY "Users can insert own rituals"
    ON public.daily_rituals FOR INSERT
    WITH CHECK (user_id IN (SELECT id FROM public.profiles WHERE user_id = auth.uid()));

CREATE POLICY "Users can update own rituals"
    ON public.daily_rituals FOR UPDATE
    USING (user_id IN (SELECT id FROM public.profiles WHERE user_id = auth.uid()));

-- Trades: Users can read/write only their own trades
CREATE POLICY "Users can view own trades"
    ON public.trades FOR SELECT
    USING (user_id IN (SELECT id FROM public.profiles WHERE user_id = auth.uid()));

CREATE POLICY "Users can insert own trades"
    ON public.trades FOR INSERT
    WITH CHECK (user_id IN (SELECT id FROM public.profiles WHERE user_id = auth.uid()));

CREATE POLICY "Users can update own trades"
    ON public.trades FOR UPDATE
    USING (user_id IN (SELECT id FROM public.profiles WHERE user_id = auth.uid()));

-- ─────────────────────────────────────────────
-- Auto-create profile on user signup
-- ─────────────────────────────────────────────
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO public.profiles (user_id, username, discipline_score, default_max_loss)
    VALUES (
        NEW.id,
        COALESCE(NEW.raw_user_meta_data->>'username', split_part(NEW.email, '@', 1)),
        0,
        0
    );
    RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Trigger to auto-create profile after signup
DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
    AFTER INSERT ON auth.users
    FOR EACH ROW
    EXECUTE FUNCTION public.handle_new_user();

-- ─────────────────────────────────────────────
-- Storage bucket for trade screenshots
-- ─────────────────────────────────────────────
-- Run this separately in Supabase dashboard or via API:
-- INSERT INTO storage.buckets (id, name, public) VALUES ('trade-screenshots', 'trade-screenshots', true);
