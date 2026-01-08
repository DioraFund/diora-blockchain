import { motion } from 'framer-motion'
import { 
  ChartBarIcon, 
  ServerIcon, 
  CpuChipIcon,
  CurrencyDollarIcon 
} from '@heroicons/react/24/outline'
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts'

interface NetworkStatsProps {
  stats: {
    totalTransactions?: number
    activeValidators?: number
    tps?: number
    gasPrice?: string
    networkHashrate?: string
    difficulty?: string
  } | null
}

export function NetworkStats({ stats }: NetworkStatsProps) {
  // Mock historical data for the chart
  const chartData = [
    { time: '00:00', tps: 45 },
    { time: '04:00', tps: 52 },
    { time: '08:00', tps: 78 },
    { time: '12:00', tps: 95 },
    { time: '16:00', tps: 82 },
    { time: '20:00', tps: 68 },
    { time: '24:00', tps: 55 },
  ]

  const statCards = [
    {
      title: 'Network Hashrate',
      value: stats?.networkHashrate || 'Loading...',
      icon: CpuChipIcon,
      color: 'from-diora-purple to-diora-blue'
    },
    {
      title: 'Difficulty',
      value: stats?.difficulty || 'Loading...',
      icon: ChartBarIcon,
      color: 'from-diora-blue to-diora-cyan'
    },
    {
      title: 'Gas Price',
      value: stats?.gasPrice || 'Loading...',
      icon: CurrencyDollarIcon,
      color: 'from-diora-cyan to-green-500'
    },
    {
      title: 'Total Blocks',
      value: '1,234,567',
      icon: ServerIcon,
      color: 'from-green-500 to-diora-purple'
    }
  ]

  return (
    <div className="space-y-8">
      <div className="flex items-center justify-between">
        <h3 className="text-2xl font-bold gradient-text">Network Performance</h3>
        <div className="flex items-center space-x-2">
          <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse" />
          <span className="text-sm text-dark-text-secondary">Live</span>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-6">
        {statCards.map((card, index) => (
          <motion.div
            key={card.title}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: index * 0.1 }}
            className="card"
          >
            <div className="flex items-center justify-between mb-4">
              <div className={`p-3 bg-gradient-to-r ${card.color} rounded-xl`}>
                <card.icon className="w-6 h-6 text-white" />
              </div>
              <div className="text-right">
                <div className="text-2xl font-bold">{card.value}</div>
                <div className="text-sm text-dark-text-secondary">{card.title}</div>
              </div>
            </div>
          </motion.div>
        ))}
      </div>

      {/* TPS Chart */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.4 }}
        className="card"
      >
        <h4 className="text-xl font-semibold mb-6 flex items-center">
          <ChartBarIcon className="w-5 h-5 mr-2 text-diora-cyan" />
          Transactions Per Second (24h)
        </h4>
        
        <div className="h-64">
          <ResponsiveContainer width="100%" height="100%">
            <LineChart data={chartData}>
              <CartesianGrid strokeDasharray="3 3" stroke="#262626" />
              <XAxis 
                dataKey="time" 
                stroke="#a3a3a3"
                style={{ fontSize: '12px' }}
              />
              <YAxis 
                stroke="#a3a3a3"
                style={{ fontSize: '12px' }}
              />
              <Tooltip 
                contentStyle={{
                  backgroundColor: '#1a1a1a',
                  border: '1px solid #262626',
                  borderRadius: '8px',
                }}
                labelStyle={{ color: '#fafafa' }}
              />
              <Line 
                type="monotone" 
                dataKey="tps" 
                stroke="url(#colorGradient)" 
                strokeWidth={3}
                dot={{ fill: '#06b6d4', r: 4 }}
                activeDot={{ r: 6 }}
              />
              <defs>
                <linearGradient id="colorGradient" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stopColor="#8b5cf6" />
                  <stop offset="50%" stopColor="#3b82f6" />
                  <stop offset="100%" stopColor="#06b6d4" />
                </linearGradient>
              </defs>
            </LineChart>
          </ResponsiveContainer>
        </div>

        <div className="mt-4 flex items-center justify-between text-sm">
          <div className="text-dark-text-secondary">
            Average: <span className="text-diora-cyan font-semibold">68.5 TPS</span>
          </div>
          <div className="text-dark-text-secondary">
            Peak: <span className="text-green-500 font-semibold">95 TPS</span>
          </div>
        </div>
      </motion.div>

      {/* Network Health */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.6 }}
        className="grid grid-cols-1 lg:grid-cols-2 gap-6"
      >
        <div className="card">
          <h4 className="text-lg font-semibold mb-4">Network Health</h4>
          <div className="space-y-3">
            <div className="flex items-center justify-between">
              <span className="text-dark-text-secondary">Status</span>
              <span className="badge badge-success">Healthy</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-dark-text-secondary">Uptime (24h)</span>
              <span className="text-green-500 font-semibold">99.98%</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-dark-text-secondary">Finality</span>
              <span className="font-semibold">6 seconds</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-dark-text-secondary">Block Time</span>
              <span className="font-semibold">6.0s ±0.2s</span>
            </div>
          </div>
        </div>

        <div className="card">
          <h4 className="text-lg font-semibold mb-4">Validator Activity</h4>
          <div className="space-y-3">
            <div className="flex items-center justify-between">
              <span className="text-dark-text-secondary">Active Validators</span>
              <span className="font-semibold">{stats?.activeValidators || 'Loading...'}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-dark-text-secondary">Participation Rate</span>
              <span className="text-green-500 font-semibold">94.2%</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-dark-text-secondary">Total Staked</span>
              <span className="font-semibold">45.2M DIO</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-dark-text-secondary">APY</span>
              <span className="text-diora-cyan font-semibold">10.5%</span>
            </div>
          </div>
        </div>
      </motion.div>
    </div>
  )
}
